<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Membership;
use App\Models\Members;
use App\Models\TimeIn;  
use App\Models\MembersMemberships;
use Carbon\Carbon;

class MemberController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status'); // 'active', 'expired', or null

        // Subquery to get latest membership for each member
        $latestMemberships = \DB::table('users_memberships as um1')
            ->select('um1.*')
            ->whereRaw('um1.id = (
                SELECT um2.id FROM users_memberships as um2
                WHERE um2.member_id = um1.member_id
                ORDER BY um2.start_date DESC, um2.id DESC
                LIMIT 1
            )');

        // Base member query
        $members = Members::select(
                'members.first_name', 
                'members.middle_name', 
                'members.last_name', 
                'memberships.name', 
                'latest_um.end_date', 
                'latest_um.start_date', 
                'members.id', 
                'memberships.type',
                'latest_um.duration',
                \DB::raw('(CASE WHEN memberships.type = 1 THEN 
                    (SELECT COUNT(*) FROM member_in 
                    WHERE member_in.member_id = members.id 
                    AND member_in.date >= latest_um.start_date) 
                ELSE NULL END) as session_count')
            )
            ->leftJoinSub($latestMemberships, 'latest_um', function($join) {
                $join->on('latest_um.member_id', '=', 'members.id');
            })
            ->leftJoin('memberships', 'latest_um.memberships_id', '=', 'memberships.id');

        // Search by name or membership name
        if ($search) {
            $members->where(function ($query) use ($search) {
                $query->where('members.first_name', 'like', "%{$search}%")
                    ->orWhere('members.middle_name', 'like', "%{$search}%")
                    ->orWhere('members.last_name', 'like', "%{$search}%")
                    ->orWhere('memberships.name', 'like', "%{$search}%");
            });
        }

        // Status filter
        if ($status === 'active') {
            $members->where(function($q) {
                $q->where(function($q) {
                    $q->where('memberships.type', 0)
                    ->where('latest_um.end_date', '>=', now());
                })->orWhere(function($q) {
                    $q->where('memberships.type', 1)
                    ->whereRaw('(latest_um.duration - (
                        SELECT COUNT(*) FROM member_in 
                        WHERE member_in.member_id = members.id 
                        AND member_in.date >= latest_um.start_date
                    )) > 0');
                });
            });
        } elseif ($status === 'expired') {
            $members->where(function($q) {
                $q->where(function($q) {
                    $q->where('memberships.type', 0)
                    ->where('latest_um.end_date', '<', now());
                })->orWhere(function($q) {
                    $q->where('memberships.type', 1)
                    ->whereRaw('(latest_um.duration - (
                        SELECT COUNT(*) FROM member_in 
                        WHERE member_in.member_id = members.id 
                        AND member_in.date >= latest_um.start_date
                    )) <= 0');
                });
            });
        }

        // Paginate with query string preserved
        $membersPaginated = $members->paginate(10)->withQueryString();

        $memberships = Membership::all();

        return Inertia::render('Members/Members', [
            'members' => $membersPaginated,
            'memberships' => $memberships,
            'totalItems' => $membersPaginated->total(),
            'currentRange' => sprintf('%d-%d',
                $membersPaginated->firstItem(),
                $membersPaginated->lastItem()
            ),
            'filters' => [
                'search' => $search,
                'status' => $status,
            ],
        ]);
    }


    public function addMembers() {
        $memberships = Membership::all();

        return Inertia::render('Members/Add', [
            'memberships' => $memberships,
        ]);
    }

    public function saveMember(Request $r) {
        $member = new Members;

        $member->first_name = $r->first_name;
        $member->last_name = $r->last_name;
        $member->middle_name = $r->middle_name;
        $member->gender = $r->gender;
        $member->birth_date = $r->birthdate;
        $member->age = Carbon::parse($r->birthdate)->age;
        $member->contact = $r->contact;
        $member->rfid = $r->rfid;
        $member->save();

        $membership = Membership::findOrFail($r->membership);

        $user_membership = new MembersMemberships;
        $user_membership->member_id = $member->id;
        $user_membership->memberships_id = $r->membership;
        $user_membership->fee = $membership->fee;
        if($membership->type == 1) {
            $user_membership->start_date = Carbon::now()->toDateString();
            $user_membership->end_date = Carbon::now()->toDateString();
            $user_membership->duration = $membership->duration;
        } else {
            $user_membership->start_date = Carbon::now()->toDateString();
            $user_membership->end_date = Carbon::now()->addMonths($membership->duration)->toDateString();
        }
        
        if($user_membership->save()) {
            return response()->json([
                'status' => 'success'
            ]);
        }
    }

    public function edit($id) {
        $mem = Members::findOrFail($id);
        $membership = MembersMemberships::where('member_id', $id)
            ->latest()
            ->first();

        $user_membership = Membership::findOrFail($membership->memberships_id);
        // dd($user_membership);
        $time_in = TimeIn::where('member_id', $id)
            ->whereDate('date', '>=', $membership->start_date)
            ->count();
        
        $remaining_session =  $membership->duration - $time_in;
        
        return Inertia::render('Members/Edit', [
            'mem' => $mem,
            'remaining_session' => $remaining_session,
            'membership' => $user_membership,
        ]);
    }

    public function update(Request $r) {
        $member = Members::findOrFail($r->id);

        $member->first_name = $r->first_name;
        $member->last_name = $r->last_name;
        $member->middle_name = $r->middle_name;
        $member->gender = $r->gender;
        $member->birth_date = $r->birthdate;
        $member->age = Carbon::parse($r->birthdate)->age;
        $member->contact = $r->contact;
        $member->rfid = $r->rfid;

        $membership = MembersMemberships::where('member_id', $r->id)
            ->latest('start_date')
            ->first();

        $membership_type = Membership::findOrFail($membership->memberships_id);
        
        $time_in = TimeIn::where('member_id', $r->id)
            ->whereDate('date', '>=', $membership->start_date)
            ->count();

        $membership->duration = $r->remaining_session + $time_in;
        $membership->save();
        if($member->save()) {
            return response()->json([
                'status' => 'success'
            ]);
        }
    }

    public function time(Request $r) {
        $date = Carbon::now()->toDateString();
        $member = Members::where('rfid', $r->id)->first();
        if($member) {
            $membership = MembersMemberships::where('member_id', $member->id)->first();
            if($membership) {
                $check = TimeIn::where('member_id', $member->id)->where('date', $date)->first();

                if(!$check) {
                    $time = new TimeIn();
                    $time->member_id = $member->id;

                    $time->date = $date;
                    $time->in = Carbon::now()->toTimeString();
                    $time->save();

                    return response()->json([
                        'msg' => 'Welcome, ' . $member->first_name . ' ' . $member->middle_name[0] . '. ' . $member->last_name . ' Keep Grinding'
                    ]);
                } else if (!$r->active) {
                    $check->out = Carbon::now()->toTimeString();
                    $check->save();

                    return response()->json([
                        'msg' => 'Goodbye, ' . $member->first_name . ' ' . $member->middle_name[0] . '.' . $member->last_name . ' Come Again!'
                    ]);
                } else {
                    return response()->json([
                        'msg' => 'You are already in '  . $member->first_name . ' ' . $member->middle_name[0] . '. ' . $member->last_name
                    ]);
                }
            } else {
                return response()->json([
                    'msg' => 'Please renew your Membership '  . $member->first_name . ' ' . $member->middle_name[0] . '. ' . $member->last_name
                ]);
            }
            
        }
        return response()->json([
            'msg' => 'RFID not Found'
        ]);
        
    }

    public function renew(Request $request) {
        $membership = Membership::findOrFail($request->membership);

        $user_membership = new MembersMemberships;
        $user_membership->member_id = $request->id;
        $user_membership->memberships_id = $request->membership;
        $user_membership->fee = $membership->fee;
        if($membership->type == 1) {
            $user_membership->start_date = Carbon::now()->toDateString();
            $user_membership->end_date = Carbon::now()->toDateString();
            $user_membership->duration = $membership->duration;
        } else {
            $user_membership->start_date = Carbon::now()->toDateString();
            $user_membership->end_date = Carbon::now()->addMonths($membership->duration)->toDateString();
        }

        if($user_membership->save()) {
            return response()->json([
                'status' => 'success'
            ]);
        }
    }

    public function memberIn() {
        return Inertia::render('Members/List');
    }

    public function fetchMemberIn(Request $r) {
        $date = Carbon::parse($r->date)->toDateString();
        // $date = Carbon::parse('January 3, 2025')->toDateString();
        $list = TimeIn::whereDate('date', $date)
                ->join('members', 'members.id', 'member_in.member_id')
                ->get();

        return response()->json($list);
    }
}

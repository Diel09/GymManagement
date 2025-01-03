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
    public function index() {
        // $members = Members::select('members.first_name', 'members.middle_name', 'members.last_name', 'memberships.name', 'latest_membership.end_date', 'members.id')
        //                 ->leftJoin('users_memberships as latest_membership', function($join) {
        //                     $join->on('latest_membership.member_id', '=', 'members.id')
        //                         ->whereRaw('latest_membership.end_date = (SELECT MAX(um.end_date) FROM users_memberships as um WHERE um.member_id = members.id)');
        //             })->join('memberships', 'latest_membership.memberships_id', '=', 'memberships.id')->paginate(10);

        $members = Members::select('members.first_name', 'members.middle_name', 'members.last_name', 'memberships.name', 'users_memberships.end_date', 'members.id')
                        ->leftJoin('users_memberships', function($join) {
                            $join->on('users_memberships.member_id', '=', 'members.id')
                                ->whereRaw('users_memberships.end_date = (SELECT MAX(um.end_date) FROM users_memberships as um WHERE um.member_id = members.id)');
                        })
                        ->leftJoin('memberships', 'users_memberships.memberships_id', '=', 'memberships.id')
                        ->paginate(10);

        // dd($members);
        $memberships = Membership::all();

        return Inertia::render('Members/Members', [
            'members' => $members,
            'memberships' => $memberships,
            'totalItems' => $members->total(),
            'currentRange' => sprintf('%d-%d', ($members->currentPage() - 1) * $members->perPage() + 1, min($members->currentPage() * $members->perPage(), $members->total())),
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
        $user_membership->start_date = Carbon::now()->toDateString();
        $user_membership->end_date = Carbon::now()->addMonths($membership->duration)->toDateString();

        if($user_membership->save()) {
            return response()->json([
                'status' => 'success'
            ]);
        }
    }

    public function edit($id) {
        $mem = Members::findOrFail($id);
        return Inertia::render('Members/Edit', [
            'mem' => $mem,
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
        $user_membership->start_date = Carbon::now()->toDateString();
        $user_membership->end_date = Carbon::now()->addMonths($membership->duration)->toDateString();

        if($user_membership->save()) {
            return response()->json([
                'status' => 'success'
            ]);
        }
    }
}

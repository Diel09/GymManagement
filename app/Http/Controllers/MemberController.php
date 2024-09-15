<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Membership;
use App\Models\Members;
use App\Models\MembersMemberships;
use Carbon\Carbon;

class MemberController extends Controller
{
    public function index() {
        $members = Members::select('members.first_name', 'members.middle_name', 'members.last_name', 'memberships.name', 'users_memberships.end_date', 'members.id')
                ->join('users_memberships', 'members.id', '=', 'users_memberships.member_id')
                ->join('memberships', 'users_memberships.memberships_id', 'memberships.id')
                ->paginate(10);
        // dd($members);
        return Inertia::render('Members/Members', [
            'members' => $members,
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

        if($member->save()) {
            return response()->json([
                'status' => 'success'
            ]);
        }
    }
}

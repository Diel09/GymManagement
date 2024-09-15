<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Membership;
use Redirect;

class MembershipsController extends Controller
{
    public function index() {
        $membership = Membership::paginate(10);
        return Inertia::render('Memberships/Memberships', [
            'memberships' => $membership,
            'totalItems' => $membership->total(),
            'currentRange' => sprintf('%d-%d', ($membership->currentPage() - 1) * $membership->perPage() + 1, min($membership->currentPage() * $membership->perPage(), $membership->total())),
        ]);
    }

    public function addMembership() {
        return Inertia::render('Memberships/Add');
    }

    public function saveMembership(Request $r) {
        $membership = new Membership;

        $membership->name = $r->name;
        $membership->fee = $r->fee;
        $membership->duration = $r->duration;

        if($membership->save()) {
            return response()->json([
                'status' => 'success'
            ]);
            // return Inertia::render('Memberships/Memberships');
        }
    }

    public function edit($id) {
        $membership = Membership::findOrFail($id);

        return Inertia::render('Memberships/Edit', [
            'membership' => $membership,
        ]);
    }

    public function update(Request $r) {
        $membership = Membership::findOrFail($r->id);

        $membership->name = $r->name;
        $membership->fee = $r->fee;
        $membership->duration = $r->duration;

        if($membership->save()) {
            return response()->json([
                'status' => 'success'
            ]);
            // return Inertia::render('Memberships/Memberships');
        }
    }

    public function delete($id) {
        $membership = Membership::findOrFail($id);

        if($membership->delete()) {
            return response()->json([
                'status' => 'success'
            ]);
        }
    }
}

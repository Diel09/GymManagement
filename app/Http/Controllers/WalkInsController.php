<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Membership;
use App\Models\WalkIns;
use Carbon\Carbon;

class WalkInsController extends Controller
{
    public function index() {
        $memberships = Membership::all();

        return Inertia::render('WalkIns/WalkIns', [
            'memberships' => $memberships,
        ]);
    }

    public function saveWalkin(Request $r) {
        // dd($r->all());
        $walk_in = new WalkIns;

        $walk_in->first_name = $r->first_name;
        $walk_in->last_name = $r->last_name;
        $walk_in->middle_name = $r->middle_name;
        $walk_in->contact = $r->contact;
        $walk_in->date = Carbon::now()->toDateString();
        $walk_in->amount = $r->amount;

        
        if($walk_in->save()) {
            return response()->json([
                'msg' => 'Successfully Saved',
                'status' => 'success'
            ]);
        } else {
            return response()->json([
                'msg' => 'An Error occured',
                'status' => 'error'
            ]);
        }
    }
}

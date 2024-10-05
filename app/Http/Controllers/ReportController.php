<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\WalkIns;
use App\Models\Members;
use App\Models\TimeIn;
use App\Models\MembersMemberships;

use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function dashboard() {
        $new_mem = Members::whereDate('created_at', '=', Carbon::today()->toDateString())->count();
        $walk_in = WalkIns::whereDate('date', '=', Carbon::today()->toDateString())->count();
        $member_in = TimeIn::whereDate('date', '=', Carbon::today()->toDateString())->count();

        //member sales
        $member_sales = MembersMemberships::select(
                DB::raw('DATE_FORMAT(start_date, "%m") as month'),
                DB::raw('SUM(fee) as total_amount')
            )->whereYear('start_date', Carbon::now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $mem_sales = array_fill(1, 12, 0);;

        foreach ($member_sales as $sale) {
            $monthIndex = (int) $sale->month; 
            $mem_sales[$monthIndex] = $sale->total_amount;
        }
        $member_sales = MembersMemberships::select(
                DB::raw('DATE_FORMAT(start_date, "%m") as month'),
                DB::raw('SUM(fee) as total_amount')
            )->whereYear('start_date', Carbon::now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $mem_sales = array_fill(1, 12, 0);;

        foreach ($member_sales as $sale) {
            $monthIndex = (int) $sale->month; 
            $mem_sales[$monthIndex] = $sale->total_amount;
        }
        //walk ins sale
        $walk_sales = WalkIns::select(
                DB::raw('DATE_FORMAT(date, "%m") as month'),
                DB::raw('SUM(amount) as total_amount')
            )->whereYear('date', Carbon::now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        
        $walkIn_sales = array_fill(1, 12, 0);

        foreach ($walk_sales as $sale) {
            $monthIndex = (int) $sale->month; 
            $walkIn_sales[$monthIndex] = $sale->total_amount;
        }
        
        //sale computation
        $month_labels = [
            1 => 'January',
            2 => 'February',
            3 => 'March',
            4 => 'April',
            5 => 'May',
            6 => 'June',
            7 => 'July',
            8 => 'August',
            9 => 'September',
            10 => 'October',
            11 => 'November',
            12 => 'December'
        ];
        $sales = [];

        for ($i = 1; $i <= 12; $i++) {
            $sales[$month_labels[$i]] = $mem_sales[$i] + $walkIn_sales[$i]; // Use the month name as the key
        }

        return Inertia::render('Dashboard', [
            'new_mem' => $new_mem,
            'walk_in' => $walk_in,
            'member_in' => $member_in,
            'sales' => $sales,
        ]);
    }

    public function index() {
        return Inertia::render('Reports/Report');
    }

    public function download(Request $request) {
        //guest computations
        $guest = [];
        $guest['total'] = 0;
        $from = Carbon::createFromFormat('m/d/Y', $request->start)->format('Y-m-d');
        $to = Carbon::createFromFormat('m/d/Y', $request->end)->format('Y-m-d');

        $guestSales = WalkIns::whereBetween('date', [$from, $to])->get();

        foreach($guestSales as $key => $sale) {
            $guest[$key]['date'] = $sale->date;
            $guest[$key]['name'] = $sale->first_name . ' ' . $sale->middle_name[0] . '. ' . $sale->last_name;
            $guest[$key]['amount'] = $sale->amount;
            $guest['total'] = $guest['total'] + $sale->amount;
        }

        //member computations
        $member = [];
        $member['total'] = 0;

        $memberSales = MembersMemberships::select(['users_memberships.fee', 'members.first_name', 'members.last_name', 'members.middle_name', 'memberships.name', 'users_memberships.start_date'])
                                            ->whereBetween('start_date', [$from, $to])
                                            ->join('members', 'users_memberships.member_id', 'members.id')
                                            ->join('memberships', 'users_memberships.memberships_id', 'memberships.id')->get();
        
        foreach($memberSales as $key => $sale) {
            $member[$key]['date'] = $sale->start_date;
            $member[$key]['name'] = $sale->first_name . ' ' . $sale->middle_name[0] . '. ' . $sale->last_name;
            $member[$key]['amount'] = $sale->fee;
            $member[$key]['membership'] = $sale->name;
            $member['total'] = $member['total'] + $sale->fee;
        }

        // dd($member);

        $pdf = Pdf::loadView('pdf', compact('guest', 'from', 'to', 'member'));
     
        return $pdf->stream();
    }
}

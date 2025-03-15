<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\WalkIns;
use App\Models\Members;
use App\Models\TimeIn;
use App\Models\Expenses;
use App\Models\MembersMemberships;

use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function dashboard() {
        $new_mem = Members::whereDate('created_at', '=', Carbon::today()->toDateString())->count();
        $walk_in = WalkIns::whereDate('date', '=', Carbon::today()->toDateString())->count();
        $member_in = TimeIn::whereDate('date', '=', Carbon::today()->toDateString())->count();

        $currentYear = Carbon::now()->year;
        $currentMonth = Carbon::now()->month;

        $member_sales = MembersMemberships::select(
                DB::raw('DATE_FORMAT(start_date, "%m") as month'),
                DB::raw('SUM(fee) as total_amount')
            )->whereYear('start_date', $currentYear)
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $mem_sales = array_fill(1, 12, 0);
        foreach ($member_sales as $sale) {
            $monthIndex = (int) $sale->month; 
            $mem_sales[$monthIndex] = $sale->total_amount;
        }

        $walk_sales = WalkIns::select(
                DB::raw('DATE_FORMAT(date, "%m") as month'),
                DB::raw('SUM(amount) as total_amount')
            )->whereYear('date', $currentYear)
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $walkIn_sales = array_fill(1, 12, 0);
        foreach ($walk_sales as $sale) {
            $monthIndex = (int) $sale->month; 
            $walkIn_sales[$monthIndex] = $sale->total_amount;
        }

        $month_labels = [
            1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April', 5 => 'May', 6 => 'June',
            7 => 'July', 8 => 'August', 9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
        ];

        $monthly_sales = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthly_sales[$month_labels[$i]] = $mem_sales[$i] + $walkIn_sales[$i];
        }
        //weekly sales
        $start = Carbon::now()->startOfMonth()->startOfWeek(Carbon::MONDAY); // Ensure it starts on Monday

        $member_weekly_sales = MembersMemberships::select(
                DB::raw('WEEK(start_date, 1) - WEEK("' . $start->format('Y-m-d') . '", 1) + 1 as week'),
                DB::raw('SUM(fee) as total_amount')
            )
            ->whereYear('start_date', $currentYear)
            ->whereMonth('start_date', $currentMonth)
            ->groupBy('week')
            ->orderBy('week')
            ->get();
        

        $walk_weekly_sales = WalkIns::select(
                DB::raw('WEEK(date, 1) - WEEK("' . $start->format('Y-m-d') . '", 1) + 1 as week'),
                DB::raw('SUM(amount) as total_amount')
            )->whereYear('date', $currentYear)
            ->whereMonth('date', $currentMonth)
            ->groupBy('week')
            ->orderBy('week')
            ->get();
        
        // Initialize array for the number of weeks in the current month
        $weeksInMonth = ceil(Carbon::now()->daysInMonth / 7);
        $weekly_sales = array_fill(1, $weeksInMonth, 0);
        
        foreach ($member_weekly_sales as $sale) {
            if ($sale->week > 0 && $sale->week <= $weeksInMonth) {
                $weekly_sales[$sale->week] += $sale->total_amount;
            }
        }
        
        foreach ($walk_weekly_sales as $sale) {
            if ($sale->week > 0 && $sale->week <= $weeksInMonth) {
                $weekly_sales[$sale->week] += $sale->total_amount;
            }
        }
        // Daily report
        $member_daily_sales = MembersMemberships::select(
                DB::raw('DAY(start_date) as day'),
                DB::raw('SUM(fee) as total_amount')
            )->whereYear('start_date', $currentYear)
            ->whereMonth('start_date', $currentMonth)
            ->groupBy('day')
            ->orderBy('day')
            ->get();
        
        $walk_daily_sales = WalkIns::select(
                DB::raw('DAY(date) as day'),
                DB::raw('SUM(amount) as total_amount')
            )->whereYear('date', $currentYear)
            ->whereMonth('date', $currentMonth)
            ->groupBy('day')
            ->orderBy('day')
            ->get();
        
        // Initialize array for days in the current month
        $daysInMonth = Carbon::now()->daysInMonth;
        $daily_sales = array_fill(1, $daysInMonth, 0);
        
        foreach ($member_daily_sales as $sale) {
            if ($sale->day > 0 && $sale->day <= $daysInMonth) {
                $daily_sales[$sale->day] += $sale->total_amount;
            }
        }
        
        foreach ($walk_daily_sales as $sale) {
            if ($sale->day > 0 && $sale->day <= $daysInMonth) {
                $daily_sales[$sale->day] += $sale->total_amount;
            }
        }
        // dd($daily_sales);
        return Inertia::render('Dashboard', [
            'new_mem' => $new_mem,
            'walk_in' => $walk_in,
            'member_in' => $member_in,
            'monthlySales' => $monthly_sales,
            'weeklySales' => $weekly_sales,
            'dailySales' => $daily_sales
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
        $expenses = Expenses::whereBetween('date_spend', [$from, $to])->get();
        // dd($expenses);
        $expense = [];
        $expense['total'] = 0;
        foreach($expenses as $key => $ex) {
            $expense[$key]['title'] = $ex->title;
            $expense[$key]['price'] = $ex->price;
            $expense[$key]['date_spend'] = $ex->date_spend;
            $expense['total'] = $expense['total'] + $ex->price;
        }
        // dd($expense);

        $pdf = Pdf::loadView('pdf', compact('guest', 'from', 'to', 'member', 'expense'));
     
        return $pdf->stream();
    }
}

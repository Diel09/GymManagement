<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Membership;
use App\Models\Members;
use App\Models\Expenses;  
use App\Models\MembersMemberships;
use Carbon\Carbon;

class ExpensesController extends Controller
{
    public function index() {
        $expenses = Expenses::paginate(10);

        return Inertia::render('Expenses/Expenses', [
            'expenses' => $expenses,
            'totalItems' => $expenses->total(),
            'currentRange' => sprintf('%d-%d', ($expenses->currentPage() - 1) * $expenses->perPage() + 1, min($expenses->currentPage() * $expenses->perPage(), $expenses->total())),
        ]);
    }

    public function addExpense() {
        return Inertia::render('Expenses/Add');
    }

    public function saveExpense(Request $r) {
        // dd($r->all());
        $expense = new Expenses;

        $expense->title = $r->name;
        $expense->price = $r->price;
        $expense->date_spend = $r->date;
        
        if($expense->save()) {
            return response()->json([
                'status' => 'success'
            ]);
        }
    }

    public function editExpense($id) {
        // dd($id);
        $expense = Expenses::findOrFail($id);
        return Inertia::render('Expenses/Edit', [
            'expense' => $expense,
        ]);
    }

    public function updateExpense(Request $r) {
        $expense = Expenses::findOrFail($r->id);

        $expense->title = $r->name;
        $expense->price = $r->price;
        $expense->date_spend = $r->date;
        if($expense->save()) {
            return response()->json([
                'status' => 'success'
            ]);
        }
    }
}

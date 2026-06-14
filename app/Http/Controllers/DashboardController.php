<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CustomerRequest;
use App\Models\CustomerRequestDetail;
use App\Models\purchase_order;
use App\Models\Inventory;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        if (auth()->check() && auth()->user()->position === 'kepala_plant') {
            return redirect()->route('plant_schedule');
        }

        // 1. KPIs
        $totalActiveCR = CustomerRequest::where('status', '!=', 'rejected')->count();
        $totalPendingCR = CustomerRequest::where('status', 'waiting_approval')->count();
        $totalPendingPO = purchase_order::where('status', 'pending')->count();
        $lowStockMaterials = Inventory::where('stock', '<=', 1000)->get();
        $lowStockCount = $lowStockMaterials->count();

        // 2. Recent 5 Customer Requests
        $recentCR = CustomerRequest::with('user')
                        ->latest()
                        ->limit(5)
                        ->get();

        // 3. Chart 1: Monthly CR Count (Last 6 Months)
        $monthlyTrendLabels = [];
        $monthlyTrendData = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthName = $date->format('M Y');
            $count = CustomerRequest::whereMonth('created_at', $date->month)
                                    ->whereYear('created_at', $date->year)
                                    ->count();
            
            $monthlyTrendLabels[] = $monthName;
            $monthlyTrendData[] = $count;
        }

        // 4. Chart 2: Top 5 Grade Beton Ordered
        $popularGradesData = CustomerRequestDetail::with('grade')
            ->select('grade_id', DB::raw('count(*) as total_orders'))
            ->groupBy('grade_id')
            ->orderByDesc('total_orders')
            ->limit(5)
            ->get();

        $donutLabels = [];
        $donutData = [];
        foreach ($popularGradesData as $pg) {
            $donutLabels[] = $pg->grade ? $pg->grade->name_grade : 'Unknown';
            $donutData[] = $pg->total_orders;
        }

        return view('dashboard', compact(
            'totalActiveCR', 
            'totalPendingCR', 
            'totalPendingPO', 
            'lowStockCount', 
            'lowStockMaterials',
            'recentCR',
            'monthlyTrendLabels',
            'monthlyTrendData',
            'donutLabels',
            'donutData'
        ));
    }
}

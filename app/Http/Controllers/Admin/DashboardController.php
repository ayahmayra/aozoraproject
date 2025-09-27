<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Organization;
use App\Models\Invoice;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Get pending parents count
        $pendingParentsCount = User::whereHas('roles', function($q) {
            $q->where('name', 'parent');
        })->where('status', 'pending')->count();

        // Get organization info
        $organization = Organization::first();

        // Get stats
        $totalAdmins = User::whereHas('roles', function($q) {
            $q->where('name', 'admin');
        })->count();

        $totalParents = User::whereHas('roles', function($q) {
            $q->where('name', 'parent');
        })->count();

        $activeParents = User::whereHas('roles', function($q) {
            $q->where('name', 'parent');
        })->where('status', 'active')->count();

        // Get payment statistics
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;
        
        // Current month total (verified payments only)
        $currentMonthTotal = Invoice::where('payment_status', 'verified')
            ->whereMonth('billing_period_start', $currentMonth)
            ->whereYear('billing_period_start', $currentYear)
            ->sum('paid_amount');

        // Current year total (verified payments only)
        $currentYearTotal = Invoice::where('payment_status', 'verified')
            ->whereYear('billing_period_start', $currentYear)
            ->sum('paid_amount');

        // Overall total (verified payments only)
        $overallTotal = Invoice::where('payment_status', 'verified')
            ->sum('paid_amount');

        // Invoice status statistics for current year
        $totalInvoices = Invoice::whereYear('billing_period_start', $currentYear)->count();
        $verifiedInvoices = Invoice::where('payment_status', 'verified')
            ->whereYear('billing_period_start', $currentYear)->count();
        $paidInvoices = Invoice::where('payment_status', 'paid')
            ->whereYear('billing_period_start', $currentYear)->count();
        $pendingInvoices = Invoice::where('payment_status', 'pending')
            ->whereYear('billing_period_start', $currentYear)->count();

        // Month names for display
        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        return view('admin.dashboard', compact(
            'pendingParentsCount',
            'organization',
            'totalAdmins',
            'totalParents',
            'activeParents',
            'currentMonthTotal',
            'currentYearTotal',
            'overallTotal',
            'totalInvoices',
            'verifiedInvoices',
            'paidInvoices',
            'pendingInvoices',
            'currentMonth',
            'currentYear',
            'months'
        ));
    }
}

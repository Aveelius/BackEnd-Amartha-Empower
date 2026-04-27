<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Loan;
use App\Models\LearningModule;
use App\Models\Notification;
use App\Models\OjkReport;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function home(): JsonResponse
    {
        return response()->json([
            'hero' => [
                'title' => 'Amartha Empower',
                'subtitle' => 'Aplikasi pembiayaan dan pendampingan usaha perempuan.',
            ],
            'feature_buttons' => [
                ['label' => 'Ajukan Pinjaman', 'key' => 'loan', 'color' => 'coral'],
                ['label' => 'Belajar & Komunitas', 'key' => 'learning', 'color' => 'mint'],
            ],
            'quick_cards' => [
                ['title' => 'Pilihan Tenor', 'description' => '6, 12, 18, 24 bulan'],
                ['title' => 'Investasi Ala Gen Z', 'description' => 'Modul ringan dan praktis'],
            ],
            'learning_preview' => LearningModule::query()
                ->orderBy('display_order')
                ->take(3)
                ->get(),
        ]);
    }

    public function userDashboard(Request $request): JsonResponse
    {
        $user = $request->user();
        $loan = $user->loans()->with(['installments', 'documents'])->latest()->first();
        $progress = $user->learningProgress()->with('module')->get();
        $notifications = $user->notifications()->latest()->take(5)->get();
        $nextInstallment = $loan?->installments->where('status', 'pending')->sortBy('due_date')->first();

        $paidInstallments = $loan?->installments->where('status', 'paid')->count() ?? 0;
        $totalInstallments = $loan?->installments->count() ?? 0;
        $completion = $totalInstallments > 0 ? (int) round(($paidInstallments / $totalInstallments) * 100) : 0;

        return response()->json([
            'user' => $user,
            'loan_summary' => $loan ? [
                'application_code' => $loan->application_code,
                'status' => $loan->status,
                'amount' => $loan->amount,
                'tenor_months' => $loan->tenor_months,
                'next_due_date' => $nextInstallment?->due_date?->format('Y-m-d'),
                'next_installment_amount' => $nextInstallment?->amount,
                'repayment_progress' => $completion,
            ] : null,
            'learning_progress' => $progress,
            'notifications' => $notifications,
            'menu' => [
                'Ajukan Pinjaman Cepat',
                'Ruang Belajar',
                'Komunitas Usaha Perempuan',
            ],
        ]);
    }

    public function adminDashboard(): JsonResponse
    {
        $pendingLoans = Loan::query()->whereIn('status', ['submitted', 'reviewed'])->count();
        $activeLoans = Loan::query()->whereIn('status', ['approved', 'disbursed', 'ongoing'])->count();
        $femaleBorrowers = User::query()->where('role', 'user')->where('gender', 'female')->count();
        $latestReport = OjkReport::query()->latest('report_date')->first();

        return response()->json([
            'metrics' => [
                'pending_loans' => $pendingLoans,
                'active_loans' => $activeLoans,
                'female_borrowers' => $femaleBorrowers,
                'total_users' => User::query()->where('role', 'user')->count(),
            ],
            'recent_applications' => Loan::query()
                ->with('user')
                ->latest()
                ->take(5)
                ->get(),
            'latest_ojk_report' => $latestReport,
            'recent_notifications' => Notification::query()->latest()->take(5)->get(),
        ]);
    }
}

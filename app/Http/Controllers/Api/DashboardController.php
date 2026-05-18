<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Loan;
use App\Models\LoanPayment;
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

        $payoffAmount = $loan ? round((float) $loan->amount * (1 + ((float) $loan->interest_rate / 100)), 2) : 0;
        $verifiedPaymentAmount = $loan ? (float) $loan->payments()->where('status', 'verified')->sum('amount') : 0;
        $pendingPaymentAmount = $loan ? (float) $loan->payments()->where('status', 'pending')->sum('amount') : 0;
        $completion = $payoffAmount > 0 ? min(100, (int) round(($verifiedPaymentAmount / $payoffAmount) * 100)) : 0;

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
                'payoff_amount' => $payoffAmount,
                'verified_payment_amount' => $verifiedPaymentAmount,
                'pending_payment_amount' => $pendingPaymentAmount,
                'remaining_amount' => max(0, $payoffAmount - $verifiedPaymentAmount),
            ] : null,
            'payment_history' => $loan ? $loan->payments()->latest()->take(5)->get() : [],
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
                ->with(['user', 'documents'])
                ->latest()
                ->take(5)
                ->get(),
            'pending_payments' => LoanPayment::query()
                ->with(['user', 'loan'])
                ->where('status', 'pending')
                ->latest()
                ->take(8)
                ->get(),
            'latest_ojk_report' => $latestReport,
            'recent_notifications' => Notification::query()->latest()->take(5)->get(),
        ]);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Loan;
use App\Models\Notification;
use App\Models\OjkReport;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function users(): JsonResponse
    {
        return response()->json([
            'data' => User::query()->where('role', 'user')->latest()->get(),
        ]);
    }

    public function sendNotification(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'title' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string'],
            'type' => ['nullable', 'string', 'max:100'],
        ]);

        $notification = Notification::create([
            ...$validated,
            'type' => $validated['type'] ?? 'admin',
        ]);

        return response()->json([
            'message' => 'Notifikasi berhasil dikirim.',
            'data' => $notification,
        ], 201);
    }

    public function ojkReport(): JsonResponse
    {
        $activeStatuses = ['approved', 'disbursed', 'ongoing'];
        $femaleBorrowers = User::query()->where('role', 'user')->where('gender', 'female')->count();
        $maleBorrowers = User::query()->where('role', 'user')->where('gender', 'male')->count();
        $activeLoans = Loan::query()->whereIn('status', $activeStatuses)->count();
        $totalDisbursed = Loan::query()->whereIn('status', $activeStatuses)->sum('amount');
        $totalOutstanding = DB::table('installments')
            ->join('loans', 'installments.loan_id', '=', 'loans.id')
            ->whereIn('loans.status', $activeStatuses)
            ->where('installments.status', 'pending')
            ->sum('installments.amount');

        $report = OjkReport::query()->updateOrCreate(
            ['report_date' => now()->toDateString()],
            [
                'female_borrowers' => $femaleBorrowers,
                'male_borrowers' => $maleBorrowers,
                'active_loans' => $activeLoans,
                'total_disbursed' => $totalDisbursed,
                'total_outstanding' => $totalOutstanding,
            ]
        );

        return response()->json([
            'message' => 'Laporan OJK berhasil dibuat.',
            'data' => $report,
        ]);
    }
}

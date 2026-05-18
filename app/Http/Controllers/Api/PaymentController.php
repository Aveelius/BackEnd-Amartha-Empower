<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Loan;
use App\Models\LoanPayment;
use App\Models\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PaymentController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $payments = $request->user()->role === 'admin'
            ? LoanPayment::query()->with(['user', 'loan'])->latest()->get()
            : LoanPayment::query()->with('loan')->where('user_id', $request->user()->id)->latest()->get();

        return response()->json(['data' => $payments]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:1000'],
            'payment_method' => ['required', Rule::in(['bank', 'ewallet'])],
            'payment_provider' => ['required_if:payment_method,bank', 'nullable', Rule::in(['BCA', 'BRI', 'BNI', 'Mandiri', 'BSI'])],
            'proof_payment' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ]);

        $loan = $request->user()->loans()
            ->whereIn('status', ['approved', 'disbursed', 'ongoing'])
            ->latest()
            ->first();

        if (!$loan) {
            return response()->json([
                'message' => 'Belum ada pinjaman aktif yang bisa dibayar.',
            ], 422);
        }

        $payoffAmount = $this->payoffAmount($loan);
        $acceptedPaymentAmount = (float) $loan->payments()
            ->whereIn('status', ['pending', 'verified'])
            ->sum('amount');
        $remainingAmount = max(0, $payoffAmount - $acceptedPaymentAmount);

        if ((float) $validated['amount'] > $remainingAmount) {
            return response()->json([
                'message' => 'Masukkan nominal yang tepat',
            ], 422);
        }

        $file = $request->file('proof_payment');
        $payment = LoanPayment::create([
            'loan_id' => $loan->id,
            'user_id' => $request->user()->id,
            'amount' => $validated['amount'],
            'payment_method' => $validated['payment_method'],
            'payment_provider' => $validated['payment_method'] === 'bank' ? $validated['payment_provider'] : null,
            'proof_file_name' => $file->getClientOriginalName(),
            'proof_file_path' => $file->store('payment-proofs', 'public'),
            'status' => 'pending',
        ]);

        Notification::create([
            'user_id' => $request->user()->id,
            'title' => 'Pembayaran dikirim',
            'message' => 'Bukti pembayaran Anda sedang menunggu verifikasi admin.',
            'type' => 'payment',
        ]);

        return response()->json([
            'message' => 'Pembayaran berhasil dikirim dan menunggu verifikasi admin.',
            'data' => $payment->load(['loan', 'user']),
        ], 201);
    }

    public function updateStatus(Request $request, LoanPayment $payment): JsonResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['verified', 'rejected'])],
            'admin_notes' => ['nullable', 'string'],
        ]);

        DB::transaction(function () use ($payment, $validated) {
            $payment->update([
                'status' => $validated['status'],
                'verified_at' => $validated['status'] === 'verified' ? now() : null,
                'admin_notes' => $validated['admin_notes'] ?? null,
            ]);

            $loan = $payment->loan()->lockForUpdate()->first();
            $payoffAmount = $this->payoffAmount($loan);
            $verifiedAmount = (float) $loan->payments()->where('status', 'verified')->sum('amount');

            if ($validated['status'] === 'verified') {
                $this->syncInstallments($loan, $verifiedAmount);
                $loan->update([
                    'status' => $verifiedAmount >= $payoffAmount ? 'completed' : 'ongoing',
                ]);
            }

            Notification::create([
                'user_id' => $payment->user_id,
                'title' => 'Pembayaran '.$validated['status'],
                'message' => 'Pembayaran sebesar Rp '.number_format((float) $payment->amount, 0, ',', '.').' telah '.$validated['status'].'.',
                'type' => 'payment-status',
            ]);
        });

        return response()->json([
            'message' => 'Status pembayaran berhasil diperbarui.',
            'data' => $payment->fresh(['loan', 'user']),
        ]);
    }

    private function payoffAmount(Loan $loan): float
    {
        return round((float) $loan->amount * (1 + ((float) $loan->interest_rate / 100)), 2);
    }

    private function syncInstallments(Loan $loan, float $verifiedAmount): void
    {
        $remaining = $verifiedAmount;

        foreach ($loan->installments()->orderBy('sequence')->get() as $installment) {
            if ($remaining >= (float) $installment->amount) {
                $installment->update([
                    'status' => 'paid',
                    'paid_at' => $installment->paid_at ?? now()->toDateString(),
                ]);
                $remaining -= (float) $installment->amount;
            }
        }
    }
}

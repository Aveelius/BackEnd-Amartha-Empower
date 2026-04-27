<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Installment;
use App\Models\Loan;
use App\Models\LoanDocument;
use App\Models\Notification;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class LoanController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $loans = $request->user()->role === 'admin'
            ? Loan::query()->with('user')->latest()->get()
            : $request->user()->loans()->with(['documents', 'installments'])->latest()->get();

        return response()->json(['data' => $loans]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:1000000', 'max:10000000'],
            'tenor_months' => ['required', Rule::in([6, 12, 18, 24])],
            'documents' => ['nullable', 'array'],
            'documents.*.document_type' => ['required_with:documents', 'string', 'max:100'],
            'documents.*.file_name' => ['required_with:documents', 'string', 'max:255'],
        ]);

        $loan = DB::transaction(function () use ($request, $validated) {
            $loan = Loan::create([
                'user_id' => $request->user()->id,
                'application_code' => 'AE-'.strtoupper(Str::random(8)),
                'amount' => $validated['amount'],
                'tenor_months' => $validated['tenor_months'],
                'status' => 'submitted',
                'submitted_at' => now()->toDateString(),
            ]);

            foreach ($validated['documents'] ?? [] as $document) {
                LoanDocument::create([
                    'loan_id' => $loan->id,
                    'document_type' => $document['document_type'],
                    'file_name' => $document['file_name'],
                ]);
            }

            Notification::create([
                'user_id' => $request->user()->id,
                'title' => 'Pengajuan diterima',
                'message' => 'Pengajuan pinjaman Anda sedang diverifikasi admin.',
                'type' => 'loan',
            ]);

            return $loan->load('documents');
        });

        return response()->json([
            'message' => 'Pengajuan pinjaman berhasil dibuat.',
            'data' => $loan,
        ], 201);
    }

    public function updateStatus(Request $request, Loan $loan): JsonResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['reviewed', 'approved', 'rejected', 'disbursed', 'ongoing', 'completed'])],
            'admin_notes' => ['nullable', 'string'],
        ]);

        DB::transaction(function () use ($loan, $validated) {
            $loan->update([
                'status' => $validated['status'],
                'admin_notes' => $validated['admin_notes'] ?? null,
                'approved_at' => in_array($validated['status'], ['approved', 'disbursed', 'ongoing', 'completed'], true) ? now()->toDateString() : $loan->approved_at,
                'disbursed_at' => in_array($validated['status'], ['disbursed', 'ongoing', 'completed'], true) ? now()->toDateString() : $loan->disbursed_at,
            ]);

            if (in_array($validated['status'], ['approved', 'disbursed', 'ongoing'], true) && $loan->installments()->count() === 0) {
                $monthlyAmount = round(($loan->amount * (1 + ($loan->interest_rate / 100))) / $loan->tenor_months, 2);

                for ($i = 1; $i <= $loan->tenor_months; $i++) {
                    Installment::create([
                        'loan_id' => $loan->id,
                        'sequence' => $i,
                        'due_date' => Carbon::now()->addMonths($i)->toDateString(),
                        'amount' => $monthlyAmount,
                        'status' => $i === 1 && $validated['status'] === 'completed' ? 'paid' : 'pending',
                    ]);
                }
            }

            Notification::create([
                'user_id' => $loan->user_id,
                'title' => 'Status pinjaman diperbarui',
                'message' => 'Pengajuan '.$loan->application_code.' sekarang berstatus '.$validated['status'].'.',
                'type' => 'loan-status',
            ]);
        });

        return response()->json([
            'message' => 'Status pinjaman berhasil diperbarui.',
            'data' => $loan->fresh(['user', 'installments', 'documents']),
        ]);
    }
}

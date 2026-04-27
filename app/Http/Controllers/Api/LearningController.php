<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LearningModule;
use App\Models\LearningProgress;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LearningController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $modules = LearningModule::query()
            ->with(['progress' => fn ($query) => $query->where('user_id', $request->user()->id)])
            ->orderBy('display_order')
            ->get();

        return response()->json(['data' => $modules]);
    }

    public function complete(Request $request, LearningModule $module): JsonResponse
    {
        $progress = LearningProgress::updateOrCreate(
            [
                'user_id' => $request->user()->id,
                'learning_module_id' => $module->id,
            ],
            [
                'completion_percent' => 100,
                'is_completed' => true,
                'completed_at' => now(),
            ]
        );

        return response()->json([
            'message' => 'Modul berhasil diselesaikan.',
            'data' => $progress->load('module'),
        ]);
    }
}

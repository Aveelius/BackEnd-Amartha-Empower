<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CommunityComment;
use App\Models\CommunityPost;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CommunityController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'data' => CommunityPost::query()
                ->with(['user', 'comments.user'])
                ->latest()
                ->get(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'category' => ['required', Rule::in(['chat', 'tip', 'event'])],
            'event_date' => ['nullable', 'date'],
            'event_location' => ['nullable', 'string', 'max:255'],
        ]);

        $post = CommunityPost::create([
            ...$validated,
            'user_id' => $request->user()->id,
        ])->load('user');

        return response()->json([
            'message' => 'Posting komunitas berhasil dibuat.',
            'data' => $post,
        ], 201);
    }

    public function comment(Request $request, CommunityPost $post): JsonResponse
    {
        $validated = $request->validate([
            'content' => ['required', 'string'],
        ]);

        $comment = CommunityComment::create([
            'community_post_id' => $post->id,
            'user_id' => $request->user()->id,
            'content' => $validated['content'],
        ])->load('user');

        return response()->json([
            'message' => 'Komentar berhasil dikirim.',
            'data' => $comment,
        ], 201);
    }
}

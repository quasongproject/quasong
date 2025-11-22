<?php

namespace App\Http\Controllers;

use App\Models\Music;
use App\Models\MusicLike;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MusicLikeController extends Controller
{
    /**
     * Toggle like/unlike (untuk button toggle)
     */
    public function toggle(Request $request, Music $music): JsonResponse
    {
        $user = $request->user();

        try {
            DB::beginTransaction();

            $like = MusicLike::where('user_id', $user->id)
                ->where('music_id', $music->id)
                ->first();

            if ($like) {
                // Unlike
                $like->delete();
                $isLiked = false;
                $message = 'Unliked';
            } else {
                // Like
                MusicLike::create([
                    'user_id' => $user->id,
                    'music_id' => $music->id,
                ]);
                $isLiked = true;
                $message = 'Liked';
            }

            // Get updated likes count
            $likesCount = $music->likes()->count();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $message,
                'isLiked' => $isLiked,
                'likesCount' => $likesCount,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to toggle like',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Like a music
     */
    public function like(Request $request, Music $music): JsonResponse
    {
        $user = $request->user();

        try {
            $like = MusicLike::firstOrCreate([
                'user_id' => $user->id,
                'music_id' => $music->id,
            ]);

            $likesCount = $music->likes()->count();

            return response()->json([
                'success' => true,
                'message' => 'Music liked',
                'isLiked' => true,
                'likesCount' => $likesCount,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to like music',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Unlike a music
     */
    public function unlike(Request $request, Music $music): JsonResponse
    {
        $user = $request->user();

        try {
            MusicLike::where('user_id', $user->id)
                ->where('music_id', $music->id)
                ->delete();

            $likesCount = $music->likes()->count();

            return response()->json([
                'success' => true,
                'message' => 'Music unliked',
                'isLiked' => false,
                'likesCount' => $likesCount,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to unlike music',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}

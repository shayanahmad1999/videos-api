<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreVideoRequest;
use App\Http\Requests\UpdateVideoRequest;
use App\Http\Resources\API\VideoResource;
use App\Models\Video;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class VideoController extends Controller
{
    use AuthorizesRequests;

    // GET /api/videos
    public function index(Request $request)
    {
        try {
            $user = $request->user();

            $q = trim((string) $request->query('q', ''));
            $perPage = min(max((int) $request->query('per_page', 1), 1), 100);

            $query = Video::query()->where('user_id', $user->id);

            if ($q !== '') {
                $query->where('title', 'like', "%{$q}%");
            }

            $videos = $query->latest()->paginate($perPage);

            return VideoResource::collection($videos);
        } catch (\Throwable $e) {
            Log::error('Error fetching videos: ' . $e->getMessage());
            return response()->json([
                'message' => 'Unable to fetch videos at this time. Please try again later.'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    // GET /api/videos/{video}
    public function show(Request $request, Video $video)
    {
        try {
            $this->authorize('view', $video);
            return new VideoResource($video);
        } catch (\Throwable $e) {
            Log::error('Error showing video: ' . $e->getMessage());
            return response()->json([
                'message' => 'Unable to display the requested video.'
            ], Response::HTTP_FORBIDDEN);
        }
    }

    // POST /api/videos
    public function store(StoreVideoRequest $request)
    {
        try {
            $user = $request->user();

            $srcUrl = $request->string('src')->toString();
            $posterUrl = $request->string('poster')->toString();

            if ($request->hasFile('video_file')) {
                $path = $request->file('video_file')->store('videos', 'public');
                $srcUrl = Storage::disk('public')->url($path);
            }
            if ($request->hasFile('poster_file')) {
                $path = $request->file('poster_file')->store('posters', 'public');
                $posterUrl = Storage::disk('public')->url($path);
            }

            $video = new Video();
            $video->user_id = $user->id;
            $video->title   = $request->input('title');
            $video->src     = $srcUrl;
            $video->poster  = $posterUrl;
            $video->tracks  = $request->input('tracks', []);
            $video->save();

            return (new VideoResource($video))
                ->response()
                ->setStatusCode(Response::HTTP_CREATED);
        } catch (\Throwable $e) {
            Log::error('Error storing video: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to upload and save the video. Please try again.'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    // PUT/PATCH /api/videos/{video}
    public function update(UpdateVideoRequest $request, Video $video)
    {
        try {
            $this->authorize('update', $video);

            if ($request->hasFile('video_file')) {
                // delete old file if exists
                if ($video->src && Storage::disk('public')->exists(str_replace('/storage/', '', $video->src))) {
                    Storage::disk('public')->delete(str_replace('/storage/', '', $video->src));
                }
                $path = $request->file('video_file')->store('videos', 'public');
                $video->src = Storage::disk('public')->url($path);
            }
            if ($request->hasFile('poster_file')) {
                if ($video->poster && Storage::disk('public')->exists(str_replace('/storage/', '', $video->poster))) {
                    Storage::disk('public')->delete(str_replace('/storage/', '', $video->poster));
                }
                $path = $request->file('poster_file')->store('posters', 'public');
                $video->poster = Storage::disk('public')->url($path);
            }

            if ($request->has('title'))  $video->title  = $request->input('title');
            if ($request->has('src'))    $video->src    = $request->input('src');
            if ($request->has('poster')) $video->poster = $request->input('poster');
            if ($request->has('tracks')) $video->tracks = $request->input('tracks') ?? [];

            $video->save();

            return new VideoResource($video);
        } catch (\Throwable $e) {
            Log::error('Error updating video: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to update the video. Please try again.'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    // DELETE /api/videos/{video}
    public function destroy(Request $request, Video $video)
    {
        try {
            $this->authorize('delete', $video);

            // Delete associated files
            if ($video->src && Storage::disk('public')->exists(str_replace('/storage/', '', $video->src))) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $video->src));
            }
            if ($video->poster && Storage::disk('public')->exists(str_replace('/storage/', '', $video->poster))) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $video->poster));
            }

            $video->delete();

            return response()->noContent();
        } catch (\Throwable $e) {
            Log::error('Error deleting video: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to delete the video. Please try again.'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

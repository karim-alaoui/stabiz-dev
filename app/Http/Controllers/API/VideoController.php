<?php

namespace App\Http\Controllers\API;

use App\Actions\GetVideos;
use App\Http\Controllers\BaseApiController;
use App\Http\Requests\GetVideoReq;
use App\Http\Requests\StoreVideoReq;
use App\Http\Requests\UpdateVideoReq;
use App\Http\Resources\VideoResource;
use App\Models\Video;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;

/**
 * @group Videos
 *
 * CRUD apis for videos
 */
class VideoController extends BaseApiController
{
    public function __construct()
    {
        $this->authorizeResource(Video::class);
    }

    /**
     * Video list
     *
     * @param GetVideoReq $request
     * @return AnonymousResourceCollection
     */
    public function index(GetVideoReq $request): AnonymousResourceCollection
    {
        $videos = GetVideos::execute($request->validated());
        return VideoResource::collection($videos);
    }

    /**
     * Add video
     *
     * @param StoreVideoReq $request
     * @return object
     */
    public function store(StoreVideoReq $request): object
    {
        $data = $request->validated();
        $video = Video::create(Arr::set($data, 'staff_id', auth()->id()));
        return (new VideoResource($video))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Show a video details
     *
     * @param $id
     * @return VideoResource
     */
    public function show($id): VideoResource
    {
        return new VideoResource(Video::findOrFail($id));
    }

    /**
     * Update video
     *
     * @param UpdateVideoReq $request
     * @param int $id
     * @return VideoResource
     */
    public function update(UpdateVideoReq $request, int $id): VideoResource
    {
        $video = Video::findOrFail($id);
        if (count($request->validated())) $video->update($request->validated());
        return new VideoResource($video);
    }

    /**
     * Remove video.
     *
     * @param int $id
     * @return Response
     */
    public function destroy(int $id): Response
    {
        Video::where('id', $id)->delete();
        return $this->noContent();
    }
}

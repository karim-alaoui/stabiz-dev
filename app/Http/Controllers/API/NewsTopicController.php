<?php

namespace App\Http\Controllers\API;

use App\Actions\GetNewsTopic;
use App\Http\Controllers\BaseApiController;
use App\Http\Requests\CreateNewsTopicReq;
use App\Http\Requests\GetTopicReq;
use App\Http\Requests\UpdateTopicReq;
use App\Http\Resources\NewsTopicResource;
use App\Models\NewsTopic;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\LengthAwarePaginator;
use JetBrains\PhpStorm\Pure;
use App\Models\User;


/**
 * @group News and topic
 *
 * APIs related to news and topics. Only super admin can view, add, delete, update
 */
class NewsTopicController extends BaseApiController
{
    public function __construct()
    {
        $this->authorizeResource(NewsTopic::class);
    }

    /**
     * News and topics index
     *
     * Show all topics and news. This will be on the staff side.
     * @responseFile storage/responses/get_newstopic.json
     * @param GetTopicReq $request
     * @return AnonymousResourceCollection
     */
    public function index(GetTopicReq $request): AnonymousResourceCollection
    {
        /**@var LengthAwarePaginator $result */
        $result = GetNewsTopic::execute($request->all());
        return NewsTopicResource::collection($result);
    }

    /**
     * News and topic user side
     *
     * This is to be shown to the users
     * @unauthenticated
     * @queryParam page current page number for pagination Example: 2
     * @queryParam per_page the number of results to return per request. Default value is 15 Example: 15
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function indexUserSide(Request $request)
    {
        if (auth()->guard('api-staff')->check()) {
            return NewsTopicResource::collection(
                NewsTopic::latest()->paginate(
                    page: $request->get('page', 1),
                    perPage: $request->get('per_page', 15)
                )
            );
        } elseif (auth()->guard('api-organizer')->check()) {
            return NewsTopicResource::collection(
                NewsTopic::userSide()->whereJsonContains('visible_to', 'organizer')->latest()->paginate(
                    page: $request->get('page', 1),
                    perPage: $request->get('per_page', 15)
                )
            );
        } elseif (auth()->guard('api')->check()) {
            $userType = auth()->guard('api')->user()->type;
            return NewsTopicResource::collection(
                NewsTopic::userSide()->whereJsonContains('visible_to', [$userType])->latest()->paginate(
                    page: $request->get('page', 1),
                    perPage: $request->get('per_page', 15)
                )
            );
        } else {
            return NewsTopicResource::collection(
                NewsTopic::userSide()->whereJsonContains('visible_to', 'others')->latest()->paginate(
                    page: $request->get('page', 1),
                    perPage: $request->get('per_page', 15)
                )
            );
        }
    }



    /**
     * Create a news and topic
     *
     * @responseFile status=201 storage/responses/topic.json
     * @param CreateNewsTopicReq $request
     * @return JsonResponse
     */
    public function store(CreateNewsTopicReq $request): JsonResponse
    {
        $visibleTo = $request->input('visible_to', []);
        $validatedData = $request->validated();

        $new = NewsTopic::create(array_merge($validatedData, [
            'visible_to' => $visibleTo,
            'added_by_staff_id' => auth()->id()
        ]));

        $new->refresh();
        return (new NewsTopicResource($new))
            ->response()
            ->setStatusCode(201);
    }


    /**
     * News and topic
     *
     * Show a particular news and topic
     * @responseFile storage/responses/atopic.json
     */
    #[Pure] public function show(NewsTopic $news_n_topic): NewsTopicResource
    {
        return new NewsTopicResource($news_n_topic);
    }

    /**
     * Update News and topic
     *
     * @responseFile storage/responses/updated_topic.json
     * @param UpdateTopicReq $request
     * @param NewsTopic $news_n_topic
     * @return NewsTopicResource
     */
    public function update(UpdateTopicReq $request, NewsTopic $news_n_topic): NewsTopicResource
    {
        NewsTopic::where('id', $news_n_topic->id)
            ->take(1)
            ->update($request->validated());

        return new NewsTopicResource($news_n_topic->refresh());
    }

    /**
     * Delete
     *
     * @param NewsTopic $news_n_topic
     * @return Response
     * @responseFile status=204 storage/responses/delete.json
     */
    public function destroy(NewsTopic $news_n_topic): Response
    {
        $news_n_topic->delete();
        return $this->noContent();
    }
}

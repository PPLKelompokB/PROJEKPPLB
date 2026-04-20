<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Http\Requests\SearchEventRequest;
use Illuminate\Http\JsonResponse;

class EventController extends Controller
{
    public function index(SearchEventRequest $request): JsonResponse
    {
        $keyword = $request->validated('keyword');
        $perPage = $request->input('per_page', 10);
        $events = Event::search($keyword)
            ->where('status', 'published') 
            ->latest('event_date')         
            ->paginate($perPage);          

        return response()->json([
            'status' => 'success',
            'message' => 'Data event berhasil diambil.',
            'meta' => [
                'keyword_searched' => $keyword,
                'total_results' => $events->total(),
                'current_page' => $events->currentPage(),
                'last_page' => $events->lastPage()
            ],
            'data' => $events->items(), 
            'links' => [
                'next_page_url' => $events->nextPageUrl(),
                'prev_page_url' => $events->previousPageUrl()
            ]
        ], 200);
    }
}
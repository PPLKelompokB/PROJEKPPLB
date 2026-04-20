<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Http\Requests\SearchEventRequest;
use Illuminate\Http\JsonResponse;

class EventController extends Controller
{
    /**
     * Menampilkan daftar event berdasarkan pencarian.
     */
    public function index(SearchEventRequest $request): JsonResponse
    {
        // 1. Mengambil data keyword dan jumlah per halaman dari request yang sudah divalidasi
        $keyword = $request->validated('keyword');
        $perPage = $request->input('per_page', 10); // Default 10 data per halaman

        // 2. Mengeksekusi query melalui scopeSearch di Model Event
        $events = Event::search($keyword)
            ->where('status', 'published') 
            ->latest('event_date')         
            ->paginate($perPage);          

        // 3. Mengembalikan format JSON
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
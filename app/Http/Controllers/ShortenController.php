<?php

namespace UrlShortener\Http\Controllers;

use Illuminate\Http\Request;
use UrlShortener\Shortener;

class ShortenController extends Controller
{

    /**
     * Store a new shorten url
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        $this->validate($request, [
            'url' => 'required|url|max:2048',
        ]);
        $shortener = Shortener::create(['url' => $request->input('url')]);

        return response()->json(['success' => true, 'hash' => $shortener->hash]);
    }

    /**
     * Show shorten url
     *
     * @param string $hash
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($hash): \Illuminate\Http\JsonResponse
    {
        $shortener = Shortener::where('hash', $hash)->firstOrFail();

        $visit = $shortener->visits()->firstOrCreate(['date' => date('Y-m-d')]);
        $visit->update(['count' => \DB::raw('count + 1')]);

        return response()->json(['success' => true, 'url' => $shortener->url]);
    }
}

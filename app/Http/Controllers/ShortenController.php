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
        $url = Shortener::where('hash', $hash)->firstOrFail(['url'])->url;
        $success = true;

        return response()->json(compact('success', 'url'));
    }
}

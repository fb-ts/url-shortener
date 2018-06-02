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
}

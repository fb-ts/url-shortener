<?php

namespace UrlShortener\Observers;

use UrlShortener\Shortener;
use Vinkla\Hashids\Facades\Hashids;

class ShortenerObserver
{

    /**
     * Listen to the Shortener created event.
     *
     * @param Shortener $shortener
     * @return void
     */
    public function created(Shortener $shortener): void
    {
        $shortener->hash = Hashids::encode($shortener->id);
        $shortener->save();
    }
}
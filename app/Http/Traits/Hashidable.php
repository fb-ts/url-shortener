<?php

namespace UrlShortener\Http\Traits;

trait Hashidable
{
    /**
     * Get the value of the model's route key.
     *
     * @return mixed
     */
    public function getRouteKey()
    {
        return \Hashids::connection(static::class)->encode($this->getKey());
    }
}
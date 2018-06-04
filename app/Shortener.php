<?php

namespace UrlShortener;

use Illuminate\Database\Eloquent\Model;

class Shortener extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['url'];

    /**
     * Get the visits for the url
     */
    public function visits()
    {
        return $this->hasMany(Visit::class);
    }
}
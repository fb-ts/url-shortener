<?php

namespace UrlShortener;

use Illuminate\Database\Eloquent\Model;

class Visit extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['date', 'count'];

    /**
     * Get the shortener that owns the visit.
     */
    public function shortener()
    {
        return $this->belongsTo(Shortener::class);
    }
}

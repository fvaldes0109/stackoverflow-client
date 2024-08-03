<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function owner()
    {
        return $this->belongsTo(Owner::class);
    }

    public function inquires()
    {
        return $this->belongsToMany(Inquire::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Carbon\Carbon;


class Post extends Model
{
    use HasApiTokens,HasFactory;

    protected $fillable = [
        'title',
        'body',
        'image',
    ];

 // Accessor for created_at
 public function getCreatedAtAttribute($value)
 {
     return Carbon::parse($value)->format('d-m-Y H:i:s');
 }

 // Accessor for updated_at
 public function getUpdatedAtAttribute($value)
 {
     return Carbon::parse($value)->format('d-m-Y H:i:s');
 }


}

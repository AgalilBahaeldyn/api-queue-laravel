<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Owner extends Model
{
    use HasFactory;

    protected $primaryKey = 'owner_id';
    protected $table = 'owner';
    protected $fillable = [


    ];
    const CREATED_AT = null;
    const UPDATED_AT = null;
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $table = 'service';
    protected $fillable = [
        

    ];
    const CREATED_AT = null;
    const UPDATED_AT = null;
}

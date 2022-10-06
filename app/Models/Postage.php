<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Postage extends Model
{
    use HasFactory;
    protected $table = 'postage_price';
    protected $fillable = ['local_base', 'local_increment', 'international_base', 'international_increment'];
    public $timestamps = false;
}

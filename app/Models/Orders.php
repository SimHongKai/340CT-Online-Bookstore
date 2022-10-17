<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Orders extends Model
{
    use HasFactory;
    protected $primaryKey = 'orderID';
    protected $table = 'orders';
    protected $fillable = ['userID','basePrice','postagePrice','recipientName','country','state','district','postcode','address'];
    public $timestamps = false;
}

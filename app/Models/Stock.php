<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    use HasFactory;
    protected $table = 'stock';
    protected $primaryKey = 'ISBN13';
    protected $fillable = ['ISBN13',
                         'bookName', 
                         'bookAuthor', 
                         'publicationDate',
                         'bookDescription', 
                         'coverImg', 
                         'tradePrice', 
                         'retailPrice', 
                         'qty'] ;
     public $timestamps = false;
}

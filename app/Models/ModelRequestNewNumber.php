<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModelRequestNewNumber extends Model
{
    use HasFactory;
    protected $table = "request_new_number";
    protected $primaryKey = "rnn_id";
    protected $fillable = ['customer_id', 'number'];
}

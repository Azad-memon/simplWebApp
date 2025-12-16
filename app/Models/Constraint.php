<?php
// app/Models/Constraint.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Constraint extends Model
{
    protected $fillable = [
        'title',
        'status',
    ];
       public function translations()
    {
        return $this->hasMany(LanguageTranslation::class,'translatable_id');
    }

}

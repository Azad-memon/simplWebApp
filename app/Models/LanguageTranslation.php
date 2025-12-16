<?php

// app/Models/LanguageTranslation.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LanguageTranslation extends Model
{
    protected $fillable = [
        'language_id',
        'translatable_type',
        'translatable_id',
        'meta_key',
        'meta_value',
    ];

    public function language()
    {
        return $this->belongsTo(Language::class);
    }

    public function translatable()
    {
        return $this->morphTo();
    }
}

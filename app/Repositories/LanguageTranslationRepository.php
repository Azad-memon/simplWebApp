<?php


namespace App\Repositories;

use App\Models\LanguageTranslation;
use App\Repositories\Interfaces\LanguageTranslationRepositoryInterface;
use App\Models\ModelUserActivityLog;
use Auth;

class LanguageTranslationRepository implements LanguageTranslationRepositoryInterface
{
    public function all()
    {
        return LanguageTranslation::all();
    }

    public function find(int $id)
    {
        return LanguageTranslation::findOrFail($id);
    }

    public function create(array $data)
    {
    $savedata=LanguageTranslation::create($data);
    if (!empty($savedata)) {
                ModelUserActivityLog::logActivity(
                     Auth::user()->id,
                    "has added new Translation with ID " . $savedata->id
                );
            }
    return $savedata;
    }

    public function update(int $id, array $data)
    {
        $translation = $this->find($id);
        $translation->update($data);
        if ($translation) {
                ModelUserActivityLog::logActivity(
                    Auth::user()->id,
                    "has updated Translation with ID " . $id
                );
        }
        return $translation;
    }

    public function delete(int $id)
    {
        $translation = $this->find($id);
        $deletedata=$translation->delete();
         if ($deletedata) {
                ModelUserActivityLog::logActivity(
                    Auth::user()->id,
                    "has  Deleted Translation with ID " . $id
                );
        }
        return $deletedata;
    }
}


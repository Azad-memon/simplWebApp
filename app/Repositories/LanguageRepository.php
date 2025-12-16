<?php
namespace App\Repositories;

use App\Models\Language;
use App\Repositories\Interfaces\LanguageRepositoryInterface;
use App\Models\ModelUserActivityLog;
use Illuminate\Support\Facades\Auth;

class LanguageRepository implements LanguageRepositoryInterface
{
    public function all()
    {
        return Language::all();
    }

    public function find($id)
    {
        return Language::findOrFail($id);
    }

    public function create(array $data)
    {
        $language =  Language::create($data);
        if($language){

        $activityMessage = "has added new language '{$language->name}'";

        ModelUserActivityLog::logActivity(
            Auth::user()->id,
            $activityMessage
        );
      }
      return  $language;
    }

    public function update($id, array $data)
    {


        $language = Language::findOrFail($id);
        $language->update($data);

        $activityMessage =   "has updated Language with ID " . $language->id;
        ModelUserActivityLog::logActivity(
                Auth::user()->id,
                $activityMessage
            );
        return $language;
    }

    public function delete($id)
    {
          $language = Language::destroy($id);
          if($language){

          $activityMessage = "has deleted Language ID ".$id;

        ModelUserActivityLog::logActivity(
            Auth::user()->id,
            $activityMessage
        );
      }
      return  $language;
    }
}

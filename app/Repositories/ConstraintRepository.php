<?php

namespace App\Repositories;

use App\Repositories\Interfaces\ConstraintRepositoryInterface;
use App\Models\Constraint;
use App\Models\ModelUserActivityLog;
use Auth;

class ConstraintRepository implements ConstraintRepositoryInterface
{
    public function all()
    {
        return Constraint::all();
    }

    public function find($id)
    {
        return Constraint::findOrFail($id);
    }


    public function create(array $data)
    {
            $constraint=Constraint::create($data);
           if($constraint){
         $activityMessage = "has added new constraint  with ID ".$constraint->id;
        ModelUserActivityLog::logActivity(
            Auth::user()->id,
            $activityMessage
        );
      }
      return  $constraint;
    }

    public function update($id, array $data)
    {
        $constraint = Constraint::find($id);
        if ($constraint) {
           $update= $constraint->update($data);
            if ($update) {
                ModelUserActivityLog::logActivity(
                    Auth::user()->id,
                    "has updated constraint with ID " . $id
                );
        }
        return $update;
        }
        return false;
    }

    public function delete($id)
    {

        $constraint = Constraint::find($id);
        if ($constraint) {
            $delete=$constraint->delete();
            if ($delete) {
                ModelUserActivityLog::logActivity(
                     Auth::user()->id,
                    "has deleted constraint with ID " .$id
                );
        }
        return $delete;
        }
        return false;
    }
     public function dropdown()
    {
        return response()->json(
                Constraint::select('id', 'title')->get()
            );
    }
      public function getTranslation($id)
    {

     return  Constraint::with('translations')->findOrFail($id);
    }
}

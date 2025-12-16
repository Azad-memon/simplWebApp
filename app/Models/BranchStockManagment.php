<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BranchStockManagment extends Model
{
    use HasFactory;

    // Custom table name
    protected $table = 'branch_stock_managment';

    // Mass assignable fields
    protected $fillable = [
        'branch_id',
        'ingredient_id',
        'quantity',
        'type',
        'updated_by',
    ];


    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function ingredient()
    {
        return $this->belongsTo(Ingredient::class);
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}

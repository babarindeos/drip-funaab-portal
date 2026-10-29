<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminDocumentCategory extends Model
{
    use HasFactory;

    protected $fillable = ['admin_category_type_id', 'name', 'code'];


    public function admin_category_type()
    {
            return $this->belongsTo(AdminCategoryType::class, 'admin_category_type_id', 'id');
    }
}

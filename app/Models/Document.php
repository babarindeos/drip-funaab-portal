<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
       'uuid',
       'title',
       'category_id',
       'document',
       'comment',
       'uploader',
       'filesize',
       'filetype',           
    ];

    public function staff()
    {
        return $this->belongsTo(User::class, 'uploader', 'id');
    }

    public function private_messages()
    {
        return $this->hasMany(PrivateMessage::class, 'doc_id', 'id');
    }

    public function general_messages()
    {
        return $this->hasMany(GeneralMessage::class, 'doc_id', 'id');
    }

    public function contributors()
    {
        return $this->hasMany(FlowContributor::class, 'doc_id', 'id');
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'uploader', 'id');
    }

    public function workflows()
    {
        return $this->hasMany(Workflow::class, 'doc_id', 'id');
    }

    
}

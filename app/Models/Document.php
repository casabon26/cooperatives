<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Document extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['cooperative_id','file_path','document_type','visibility','uploaded_by'];

    public function cooperative()
    {
        return $this->belongsTo(Cooperative::class);
    }

    public function uploader()
    {
        return $this->belongsTo(\App\Models\User::class,'uploaded_by');
    }
}

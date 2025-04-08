<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QueryPicture extends Model
{
    use HasFactory;

    protected $table = 'queryPictures';

    protected $primaryKey = 'id';

    protected $fillable = [
        'query_id',
        'file_path',
        'file_name',
    ];

    public $timestamps = true;

    public function queryAttachment(){
        return $this->belongsTo(Query::class, 'query_id');
    }
}

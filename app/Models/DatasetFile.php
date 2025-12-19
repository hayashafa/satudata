<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DatasetFile extends Model
{
    use HasFactory;
}
class DatasetFile extends Model
{
    protected $fillable = [
        'dataset_id',
        'file_path',
        'file_type',
        'file_size'
    ];

    public function dataset()
    {
        return $this->belongsTo(Dataset::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dataset extends Model
{
    use HasFactory;

    protected $fillable = [
    'user_id',
    'title',
    'description',
    'category_id',
    'creator',
    'year',
    'file_path',
    'image',
    'status',       // <--
    'approved_at',  // <--
];

    /**
     * Relasi ke Category
     * Satu dataset punya satu kategori
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Relasi ke User (admin yang mengupload)
     * Satu dataset diupload oleh satu user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'article';

    public static $sortable = [
        'title',
        'nb_views',
        'is_pinned',
        'user_id',
        'created_at',
        'updated_at',
    ];

    /**
     * Get the user that owns this article.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

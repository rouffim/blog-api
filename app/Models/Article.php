<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Article extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'article';

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'uuid';
    }

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

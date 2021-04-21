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
     * The path where the articles images are located.
     *
     * @var string
     */
    public static $image_location = 'images/articles';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'uuid',
        'title',
        'excerpt',
        'body',
        'image_extension',
        'nb_views',
        'is_pinned',
    ];

    public static $sortable = [
        'title',
        'nb_views',
        'is_pinned',
        'users_id',
        'created_at',
        'updated_at',
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'nb_views' => 0,
        'is_pinned' => false,
    ];

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'uuid';
    }

    /**
     * Get the filename of the article.
     * @return string|null
     */
    public function getFilename(): ?string
    {
        return is_null($this->image_extension) ?
            null :
            $this->uuid . '.' . $this->image_extension;
    }

    /**
     * Get the user that owns this article.
     */
    public function user()
    {
        return $this->belongsTo(User::class, "users_id");
    }
}

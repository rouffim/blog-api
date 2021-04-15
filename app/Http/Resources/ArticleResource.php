<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ArticleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'uuid' => $this->uuid,
            'title' => $this->title,
            'excerpt' => $this->excerpt,
            'body' => $this->body,
            'image' => $this->getFilename(),
            'nb_views' => $this->nb_views,
            'is_pinned' => $this->is_pinned,
            'user' => UserResource::make($this->user),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

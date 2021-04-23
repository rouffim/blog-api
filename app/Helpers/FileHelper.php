<?php

namespace App\Helpers;


use Illuminate\Support\Facades\Storage;

class FileHelper
{
    /**
     * @param $model
     * @return string|null
     */
    static function getModelImage($model): ?string
    {
        return is_null($model) || is_null($model->image_extension) ?
            null :
            $model->uuid . '.' . $model->image_extension;
    }

    /**
     * @param $model
     * @return string|null
     */
    static function getModelImageUrl($model): ?string
    {
        $filename = FileHelper::getModelImage($model);

        if(is_null($filename)) {
            return null;
        }
        return asset(Storage::url("$model->image_location/$filename"));
    }
}

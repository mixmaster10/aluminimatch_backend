<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class LeadsUsedCasts implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return array
     */
    public function get($model, $key, $value, $attributes)
    {
        if (is_null($value)) {
            return [
                'likes'=> [],
                'comments' => [],
                'viewed_sponsor' => []
            ];
        } else {
            return json_decode($value, true);
        }
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  array  $value
     * @param  array  $attributes
     * @return string
     */
    public function set($model, $key, $value, $attributes)
    {
        if (is_null($value)) {
            return json_encode([
                'likes'=> [],
                'comments' => [],
                'viewed_sponsor' => []
            ]);
        } else {
            return json_encode($value, true);
        }
    }
}
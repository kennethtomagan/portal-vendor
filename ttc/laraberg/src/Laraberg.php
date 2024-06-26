<?php

namespace Ttc\Laraberg;

use Laravel\Nova\Fields\Field;

class Laraberg extends Field 
{
    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'laraberg';

    public function withModelClass($model) {
        return $this->withMeta([
            'resourceModel' => $model
        ]);
    }
}


<?php

namespace Ttc\MetaField;

use App\Models\Shared\Meta;
use Barryvdh\Debugbar\Facades\Debugbar;
use Illuminate\Support\Facades\Storage;
use Laravel\Nova\Http\Requests\NovaRequest;

class MetaDeleteFile
{

    protected $field;

    public function __construct($field){
        $this->field = $field;
    }

    /**
     * Delete the field's underlying file.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string|null  $disk
     * @param  string|null  $path
     * @return boolean
     */
    public function __invoke(NovaRequest $request, $model, $disk = "", $path = ""): bool
    {

        if ($this->field->value) {
            Storage::disk($this->field->getStorageDisk())->delete($this->field->value);
        }

        $this->field->attribute = null;

        if($this->field->meta_id){
            $this->field->model::where("id", $this->field->meta_id)->delete();
        }

        // Need to return otherwise the parent model will try to delete the attribute and throw an exception
        return true;

    }
}

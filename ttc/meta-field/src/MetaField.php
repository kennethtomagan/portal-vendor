<?php

namespace Ttc\MetaField;

use App\Models\Shared\Meta;
use Illuminate\Support\Facades\Log;
use Laravel\Nova\Contracts\Downloadable;
use Laravel\Nova\Fields\Field;
use Laravel\Nova\Http\Requests\NovaRequest;

class MetaField extends Field implements Downloadable
{
    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'meta-field';

    public $deleteCallback;

    protected $fieldMetaModel = Meta::class;

    public function __construct($field) {

        parent::__construct($field->name, $field->attribute);

        if(!empty($field)){
            $this->field = $field;
            $this->field->model = Meta::class;

            if(method_exists($this->field, "delete")){
                $this->deleteCallback = new MetaDeleteFile($this->field);
            }

        }

        $this->fieldMetaModel = new $this->fieldMetaModel([$field->attribute=>null]);

        return $this;

    }

    public function resolve($resource, $attribute = null){

        $attribute = $attribute ?? $this->attribute;

        $owner = get_class($resource);
        $owner_id = $resource->id;

        $meta_row = $this->fieldMetaModel::where('owner_type', $owner)
            ->where('owner_id', $owner_id)
            ->where("meta_key", $attribute)
            ->first();

        $this->field->value = $meta_row->meta_value ?? parent::resolve($resource, $attribute);
        $this->field->meta_id = $meta_row->id ?? 0;

    }



    /**
     * Hydrate the given attribute on the model based on the incoming request.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Model|\Laravel\Nova\Support\Fluent  $model
     * @param  string  $attribute
     * @param  string|null  $requestAttribute
     * @return mixed
     */
    public function fillInto(NovaRequest $request, $model, $attribute, $requestAttribute = null){

        $fieldFill = $this->field->fillInto($request, $this->fieldMetaModel, $attribute, $requestAttribute);

        return $this->fillAttribute($request, $requestAttribute ?? $this->attribute, $model, $attribute);

    }

    /**
     * Fill the model's attribute with data.
     *
     * @param  \Illuminate\Database\Eloquent\Model|\Laravel\Nova\Support\Fluent  $model
     * @param  mixed  $value
     * @param  string  $attribute
     * @return void
     */
    public function fillModelWithData(mixed $model, mixed $value, string $attribute)
    {
        $value = !is_null($this->fieldMetaModel->{$attribute}) ? $this->fieldMetaModel->{$attribute} : $value;

        if(is_null($value)){
            return;
        }

        $owner = get_class($model);
        $owner_id = $model->id;


        if(!is_array($value)){
            $value = [$value];
        }

        foreach($value as $metaValue){

            if(empty($metaValue)){

                if( $this->field->meta_id > 0){
                    $this->fieldMetaModel::where("id", $this->field->meta_id)->delete();
                }

                return;
            }

            $this->fieldMetaModel::updateOrCreate(
                [
                    "owner_type"    => $owner,
                    "owner_id"      => $owner_id,
                    "meta_key"      => $attribute,
                ],
                [
                    "meta_value"    => $metaValue
                ]
            );
        }

    }

    public function jsonSerialize(): array{

        return $this->field->jsonSerialize();

    }

}

<?php

namespace Ttc\Thingables;

use Barryvdh\Debugbar\Facades\Debugbar;
use Laravel\Nova\Fields\ResourceRelationshipGuesser;
use Laravel\Nova\Fields\Trix;
use Laravel\Nova\Nova;
use Laravel\Nova\ResourceTool;
use App\Http\Controllers\Shared\Api\TicketMessagesController;

class Thingables extends ResourceTool
{

    public $resourceModel;
    public $resource;
    public $things = [];

    public function __construct($resource)
    {

        $this->resource = $resource;

        parent::__construct();

    }

    /**
     * Get the displayable name of the resource tool.
     *
     * @return string
     */
    public function name()
    {
        return 'Thingables';
    }

    /**
     * Get the component name for the resource tool.
     *
     * @return string
     */
    public function component()
    {
        return 'thingables';
    }

    /**
     * Get the displayable name of the resource tool.
     *
     * @return mixed
     */
    public function authEmail()
    {
        return $this->withMeta(['auth' => auth()->user()]);
    }


    /**
     * the *able thing were getting
     *
     * @return $this
     * TODO change this to be generic not just messages
     */
    public function things($tmc, $resolveCallback = null)
    {

        if(!empty($this->things)){
            return $this;
        }

        if(!empty($this->resource->id)){

            $this->things = $tmc->resolve($this->resource::$model, $this->resource->id);

            if (is_callable($resolveCallback)) {

                $this->things = call_user_func($resolveCallback, $this, $tmc);

            }

        }

        return $this->withMeta(['things' => $this->things, 'thingsModel'=> $this->resource::$model]);
    }

    public function parentResource($parentResource){
        return $this->withMeta(['parentResource' => $parentResource]);
    }

    public function fieldAdditionalMeta(){
        return [
            "uniqueKey" => uniqid($this->component())
        ];
    }

    public function fields($fields)
    {

        $fieldsAsJson = [];

        foreach ($fields as $fieldKey => $field){
            $fieldJson = array_merge(
                $field->jsonSerialize(),
                [
                    "id" => $this->component().$fieldKey
                ],
                $this->fieldAdditionalMeta()
            );
            $fieldsAsJson[] = $fieldJson;
        }

        return $this->withMeta(
            [
                'fields' => $fieldsAsJson
            ]
        );
    }

}

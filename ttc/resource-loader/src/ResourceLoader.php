<?php

namespace Ttc\ResourceLoader;

use Barryvdh\Debugbar\Facades\Debugbar;
use Illuminate\Support\Facades\Log;
use Laravel\Nova\Fields\Field;
use Laravel\Nova\Fields\ResourceRelationshipGuesser;
use Laravel\Nova\Makeable;

class ResourceLoader extends Field
{
    use Makeable;
    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'resource-loader';
    public $resourceClass;
    public $resourceName;
    public $resourceModel;
    public $selectKey;
    public $selectValue;
    public $selectConditions = [];
    public $productSelections = [];

    public function __construct($name, $resource, $resourceKey = "id", $resourceValue = ['name'])
    {
        parent::__construct($name);


        if(class_exists($resource)){

            $this->resourceClass = $resource;
            $this->resourceName = $resource::uriKey();
            $this->resourceModel = $resource::newModel();

            $this->selectKey = $resourceKey;
            $this->selectValue = $resourceValue;

        }

    }

    public function prependSelections(array $selections){
        foreach ($selections as $value => $label){
            array_unshift($this->productSelections, ["value"=>$value, "label"=>$label]);
        }

        return $this;
    }

    public function appendSelections(array $selections){

        foreach ($selections as $value => $label){
            array_push($this->productSelections, ["value"=>$value, "label"=>$label]);
        }
        return $this;
    }

    public function setSelectionConditions($conditions){
        foreach($conditions as $condition){
            $this->selectConditions[] = $condition;
        }

        return $this;
    }

    public function applyConditions(){

//        Log::debug("start ".__FUNCTION__);

        foreach ($this->selectConditions as $condition){

            foreach ($condition as $conditionName => $conditionArgs){

                try{
                    if(is_array($conditionArgs) && count($conditionArgs) && is_callable([$this->resourceModel, $conditionName], true)){
                        $this->resourceModel =  $this->resourceModel->{$conditionName}(...$conditionArgs);
                    }
                    else{
                        Debugbar::info("condition  {$conditionName} NOT applied ".implode(" ", $conditionArgs));
                    }

                }catch(\Exception $e){
                    Debugbar::info("error trying method {$conditionName} doesnt exists");
                }

            }
        }


        return $this->resourceModel;
    }

    public function loadSelections(){

        $resourceItems = $this->applyConditions()->get();


        $resourceItems->each(function($item){


            if(key_exists($item->{$this->selectKey},$this->productSelections)){
                return false;
            }

            $selectValue = [];

            foreach($this->selectValue as $valueOptions){
                if(!empty($item->{$valueOptions})){
                    $selectValue[] = $item->{$valueOptions};
                }
            }

            $this->productSelections[$item->{$this->selectKey}] = [
                "label"=>implode(" ", $selectValue), "value"=>$item->{$this->selectKey}
            ];

        });

//        Log::debug(print_r($this->productSelections, true));

        return $this->productSelections;

    }

    public function jsonSerialize(): array
    {

        return array_merge(
            parent::jsonSerialize(),
            [
                'roddy' => 'roddy!!!',
                'selectOptions' => $this->loadSelections(),
                'loadResource' => $this->resourceName,
//                'resourceURL' => '/admin/tenant/'.$this->tenantId.'/'.$this->tenantAttribute,
            ]
        );
    }
}

<?php

namespace Ttc\ProductOrder;

use Illuminate\Support\Facades\Log;
use Laravel\Nova\Fields\Field;

class ProductOrder extends Field
{
    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'product-order';

    public $resourceClass;
    public $resourceName;
    public $resourceModel;
    public $resourceKey = "id";
    public $resourceValue = ["name"];
    public $productSelections = [];

    public function __construct($name, $resource)
    {
        parent::__construct($name);

        if(class_exists($resource)){

            $this->resourceClass = $resource;
            $this->resourceName = $resource::uriKey();
            $this->resourceModel = $resource::$model;

        }

    }

    public function setSelectOptions(){

        $resourceItems = $this->resourceModel::get();

        if(!is_array($this->resourceValue)){
            $this->resourceValue = [$this->resourceValue];
        }

        $resourceItems->each(function($item){
            $resourceValue = "";

            foreach($this->resourceValue as $valueOptions){
                if(!empty($item->{$valueOptions})){
                    $resourceValue .= $item->{$valueOptions};
                }
            }

            $this->productSelections[] = ["label"=>$resourceValue, "value"=>$item->{$this->resourceKey}];
        });

        return $this->productSelections;

    }

    public function setresourceKey(string $key){

        $this->resourceKey = $key;
        return $this;
    }

    public function setResourceValue(string $value){

        $this->resourceValue = [$value];
        return $this;
    }

    public function addResourceValue(string $value){

        $this->resourceValue[] = $value;
        return $this;
    }

    public function jsonSerialize(): array
    {
        return array_merge(
            parent::jsonSerialize(),
            [
                'roddy' => 'roddy!!!',
                'selectOptions' => $this->setSelectOptions(),
                'loadResource' => $this->resourceName,
//                'resourceURL' => '/admin/tenant/'.$this->tenantId.'/'.$this->tenantAttribute,
            ]
        );
    }
}

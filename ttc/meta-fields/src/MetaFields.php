<?php

namespace Ttc\MetaFields;

use App\Models\Shared\Meta;
use App\Nova\Resource;
use Barryvdh\Debugbar\Facades\Debugbar;
use Illuminate\Http\Resources\ConditionallyLoadsAttributes;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Laravel\Nova\Fields\Field;
use Laravel\Nova\Fields\FieldCollection;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Makeable;
use Laravel\Nova\ResolvesFields;

class MetaFields extends Field
{
    use Makeable, ResolvesFields, ConditionallyLoadsAttributes;
    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'meta-fields';

    protected $selectionConditions = [];
    protected $conditionsApplied = false;
    protected $model;
    protected $owner;
    protected $ownerModel;
    protected $parentKey;
    protected $fields = [];
    protected $subFieldRules = [
        "Creation"      => [],
        "Update"        => []
    ];
    protected $existingRows = [];

    public function __construct($name, $owner, $parentKey, $fields = [])
    {
        parent::__construct($name);

//        Log::debug("start " .__FILE__." ".__FUNCTION__);

        $this->parentKey    = $parentKey;
        $this->model        = new \App\Models\Shared\Meta();
        $this->owner        = $owner; //The Nova resource
        $this->ownerModel   = $this->owner->model();
        $this->fields       = new FieldCollection($fields);

        $this->button(__('Add layout'));

        // The original menu as default
        $this->menu('flexible-drop-menu');

        $this->selectionConditions = [
            ["Where" => ["owner_type", $owner::$model]],
            ["Where" => ["owner_id", $owner->id]],
            ["Where" => ["meta_key", $this->parentKey]]
        ];

        return $this;

    }

    public function fields(){
        return $this->fields;
    }

    /**
     * Set the button's label
     *
     * @param string $label
     * @return $this
     */
    public function button($label)
    {
        return $this->withMeta(['button' => $label]);
    }

    /**
     * @param string $component The name of the component to use for the menu
     *
     * @param array  $data
     *
     * @return $this
     */
    public function menu($component, $data = [])
    {
        return $this->withMeta(['menu' => compact('component', 'data')]);
    }

    /**
     *  Prevent the 'Add Layout' button from appearing more than once
     *
     * @return $this
     */
    public function limit($limit = 1)
    {
        return $this->withMeta(['limit' => $limit]);
    }

    public function collapsed(bool $value = true)
    {
        $this->withMeta(['collapsed' => $value]);
        return $this;
    }

    public function setSelectionConditions($conditions){
        foreach($conditions as $condition){
            $this->selectionConditions[] = $condition;
        }

        return $this;
    }

    public function applyConditions(){

        if($this->conditionsApplied){
            return  $this->model;
        }

        foreach ($this->selectionConditions as $condition){

            foreach ($condition as $conditionName => $conditionArgs){

                try{
                    if(is_array($conditionArgs) && count($conditionArgs) && is_callable([$this->model, $conditionName], true)){
                        $this->model = $this->model->{$conditionName}(...$conditionArgs);
                    }
                    else{
                        Log::debug("condition  {$conditionName} NOT applied ".implode(" ", $conditionArgs));
                    }

                }catch(\Exception $e){
                    Log::debug("error trying method {$conditionName} doesnt exists");
                }

            }
        }

        $this->conditionsApplied = true;

        return $this->model;
    }

    public function loadExistingRows(){

        if(!empty($this->existingRows)){
            return $this->existingRows;
        }

        $metaRows = $this->applyConditions()->with(["children"])->get();

        $this->fields = $this->updateFields();

        $metaRows->each(function($item){

            $this->existingRows[] = $this->returnRow($item->meta_value, $item->children);

        });

        return $this->existingRows;

    }

    public function returnRow($key, $childValues){
        return [
            'key' => $key,
            'fields' => $this->cloneFields($key, $childValues)
        ];
    }

    public function updateFields(){

        $request = resolve(NovaRequest::class);

        return $this->fields->onlyUpdateFields($request, $this->fields);

//        return $this->onlyUpdateFields($request, $this->fields);
    }

    public function creationFields(){
        $request = resolve(NovaRequest::class);

//        Debugbar::info($request);
//        Debugbar::error('Error!');
//        Debugbar::warning('Watch out…');
//        Debugbar::addMessage('Another message', 'mylabel');

        $this->fields = $this->fields->onlyCreateFields($request, $this->fields);

        return $this->returnRow("new", new Collection());


    }


    /**
     * Get a cloned instance with set values
     *
     * @param  string  $key
     * @param  Collection  $meta_values
     * @return FieldCollection
     */
    public function cloneFields($key, Collection $meta_values)
    {
        return $this->fields->map(function($field) use ($meta_values, $key) {

            $savedValue = $meta_values->where("meta_key", $field->attribute)->first() ?? new \StdClass();

            return $this->cloneField($field, $key, $savedValue);

        });

    }

    /**
     * Create a working field clone instance
     *
     * @param  \Laravel\Nova\Fields\Field $original
     * @return \Laravel\Nova\Fields\Field
     */
    protected function cloneField(Field $original, $key, $savedValue) {

//        Log::debug("----- start ".__FUNCTION__);

        $field = $this->setFieldMeta($original, $key, $savedValue);

        $callables = ['displayCallback','resolveCallback','fillCallback','requiredCallback'];

        foreach ($callables as $callable) {
            if(!is_a($field->$callable ?? null, \Closure::class)) continue;
            $field->$callable = $field->$callable->bindTo($field);
        }

        return $field;
    }

    public function subFieldNameBuilder($nameParts, $start, $seperator, $end){
        return $this->attribute.$start.implode($seperator, $nameParts).$end;
    }

    public function setFieldMeta($original, $key, $savedValue){

        $field = clone $original;
//        Log::debug("----- start {$field->attribute} ".__FUNCTION__);

        $field->value = $savedValue->meta_value ?? "";

        $fieldNameParts = [
            $key,
            $field->attribute,
            $savedValue->id ?? ""
        ];

//        $field->name = $this->subFieldNameBuilder($fieldNameParts, "[", "][", "]");

        $field->withMeta(
            [
                "rowKey"            => $key,
                "fieldId"           => $savedValue->id ?? "",
                "appendName"         => $this->subFieldNameBuilder($fieldNameParts, "[", "][", "]"),
                "uniqueKey"         =>  $this->subFieldNameBuilder($fieldNameParts, "-", "-", ""),
                "extraAttributes"   => [
                    "value"             => $field->value,
                ],
                "validationKey"     =>  $this->subFieldNameBuilder($fieldNameParts, ".", ".", ""),
            ]);

        return $field;
    }

    public function loadBlankFields($key = ""){

        return [
            'key' => $key,
            'fields' => $this->cloneFields($key, new Collection())
        ];
    }


    public function fill(NovaRequest $request, $model){
//        Log::debug("start ".__FUNCTION__);

        if (!$request->exists($this->attribute)) return;

        $this->value = "";

        $value = $request[$this->attribute];

        foreach ($value as $rowOrder => $metaRow){

            $metaParentFindArr = [
                "owner_type"    => $this->owner::$model,
                "owner_id"      => $this->owner->id,
                "meta_key"      => $this->parentKey,
                "meta_value"    => $rowOrder,
            ];

            //create the parent meta row
            $meta = Meta::firstOrCreate(
                $metaParentFindArr,
                $metaParentFindArr
            );

            $fieldVerb = "Update";

            if($meta->wasRecentlyCreated){
                $fieldVerb = "Create";
            }

            $this->fields = $this->fields->{"only".$fieldVerb."Fields"}($request, $this->fields);

            //load all rules for allowed fields
            $subFieldRules = $this->loadFieldsRulesForValidationByVerb($request, $fieldVerb);

            $request->validate($subFieldRules);

            $this->fields->each(function($field) use ($meta, $metaRow, $request){

                if(!isset($metaRow[$field->attribute])){
                    return true;
                }

                $fieldId = key($metaRow[$field->attribute]);
                $metaValue = trim($metaRow[$field->attribute][$fieldId]);

                if(in_array($metaValue, ["false", ""]) && $fieldId > 0){
                    $meta->children()->where("id", $fieldId)->delete();
                    return true;
                }


                $meta->children()->updateOrCreate(
                        [
                            "meta_key"      => $field->attribute,
                            "id"            => $fieldId,
                        ],
                        [
                            "meta_value"    => $metaValue,
                        ]
                    );

            });

        }

    }

    public function loadFieldRulesByVerb(NovaRequest $request, $subField, $fieldVerb){

        if($fieldVerb == "Creation"){
            $fieldVerb = "Create";
        }

        if(!method_exists($subField, "get".$fieldVerb."Rules")){
            return '';
        }

        $fieldAttribute = $subField->attribute;
        if(empty($this->subFieldRules[$fieldVerb][$fieldAttribute])){
            $this->subFieldRules[$fieldVerb] = $subField->{"get".$fieldVerb."Rules"}($request);
        }

        return $this->subFieldRules[$fieldVerb][$fieldAttribute];

    }

    public function loadFieldsRulesByVerb(NovaRequest $request, $fieldVerb){

        return $this->fields->mapWithKeys(function($field) use ($request, $fieldVerb) {

            $this->loadFieldRulesByVerb($request, $field, $fieldVerb);

            return [$field->attribute => $this->subFieldRules[$fieldVerb][$field->attribute]];

        });


    }

    public function loadFieldsRulesForValidationByVerb(NovaRequest $request, $fieldVerb){

        return $this->fields->mapWithKeys(function($field) use ($request, $fieldVerb) {

            $this->loadFieldRulesByVerb($request, $field, $fieldVerb);

            if(empty($this->subFieldRules[$fieldVerb][$field->attribute])){
                return [];
            }


            return [
                $this->parentKey.'.*.'.$field->attribute.'.*' => $this->subFieldRules[$fieldVerb][$field->attribute]
            ];

        })->toArray();


    }

    public function jsonSerialize(): array
    {
        return array_merge(
            parent::jsonSerialize(),
            [
                'roddy' => 'roddy2!!!',
                'existingRows' => $this->loadExistingRows(),
                'collapsable' => true,
                'sortable' => true,
                'resource' => $this->owner->uriKey(),
                'resourceId' => $this->owner->id ?? "new"
            ]
        );
    }

}

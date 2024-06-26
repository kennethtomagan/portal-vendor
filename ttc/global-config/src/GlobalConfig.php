<?php

namespace Ttc\GlobalConfig;

use App\Traits\NovaMenuFilters;
use App\Traits\NovaFieldHelpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Laravel\Nova\Contracts\Downloadable;
use Laravel\Nova\Contracts\Storable;
use Laravel\Nova\DeleteField;
use Laravel\Nova\Http\Requests\CreateResourceRequest;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Menu\Menu;
use Laravel\Nova\Menu\MenuGroup;
use Laravel\Nova\Menu\MenuItem;
use Laravel\Nova\Menu\MenuSection;
use Laravel\Nova\Nova;
use Laravel\Nova\Panel;
use Laravel\Nova\Tool;
use Laravel\Nova\PerformsValidation;

class GlobalConfig extends Tool
{
    use NovaMenuFilters, PerformsValidation, NovaFieldHelpers;

    public function __construct()
    {
        parent::__construct();

//        $this->getUpdateFields(resolve(NovaRequest::class));

        $this->addToMenu("Setup");

    }


    /**
     * Perform any tasks that need to happen when the tool is booted.
     *
     * @return void
     */
    public function boot()
    {

        Nova::script('global-config', __DIR__.'/../dist/js/tool.js');
        Nova::style('global-config', __DIR__.'/../dist/css/tool.css');

    }

    /**
     * Build the menu that renders the navigation links for the tool.
     *
     * @param  \Illuminate\Http\Request $request
     * @return mixed
     */
    public function menu(Request $request)
    {
        return MenuItem::make('Global Config')
            ->path('/global-config');

    }

    /** Create the inpiut fields for this page.
     * We create a dummy Nova resource so we can use all the field display functionality.
     * @param NovaRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUpdateFields(NovaRequest $request){

        //this is a dummy Nova resource so we can use all the field display functionality
        $GCResource = new Resources\GlobalConfigResource();

        $fields = $GCResource->updateFields($request);

        $fields = $fields->transform(function ($field, $key) {
            return $this->attachFieldMeta($field);
        });

//        $panels = $fields->assignDefaultPanel(Panel::defaultNameForUpdate($resource ?? $request->newResource()));

        $toReturn = [
            'panels' => $GCResource->availablePanelsForUpdate($request, $GCResource, $fields),
        ];

        return response()->json( $toReturn, 200);

    }


    /** Create the inpiut fields for this page.
     * We create a dummy Nova resource so we can use all the field display functionality.
     * @param NovaRequest $request
     */
    public function exampleInertiaPost(NovaRequest $request){
        //this is a dummy Nova resource so we can use all the field display functionality
        $GCResource = new Resources\GlobalConfigResource();

        $GCResource->authorizeToUpdate($request);
        $GCResource::validateForUpdate($request, $GCResource);

        //if we get here the request is valid and we can now submit the values to the model to save
        $updateFields = $GCResource->updateFields($request);

        //save
        foreach($updateFields as $field){

            if (empty($field->attribute)) continue;

            if ($field->isReadonly($request)) continue;

            $tempResource =  new \stdClass;
            $field->fill($request, $tempResource);

            if (!property_exists($tempResource, $field->attribute)) continue;

            $settingField = \Ttc\GlobalConfig\Http\Model\GlobalConfig::firstOrNew([
                "key" => $field->attribute,
            ]);

            $settingField->value = $tempResource->{$field->attribute};

            Log::debug($settingField->value);

            $settingField->save();

        }

        return response()->json( [], 200);


    }

    public function deleteField(NovaRequest $request, $fieldName){

        $resource = new Resources\GlobalConfigResource();

        $field = $resource->updateFields($request)->findFieldByAttribute($fieldName, function () {
            abort(404);
        });

        $resource->authorizeToUpdate($request);

        $fieldModel = $this->loadFirstFieldModel($field->attribute);

        $filePath = $field->getStorageDisk().'/'.$fieldModel->value;

        if(Storage::exists($filePath)){
            Storage::delete($field->getStorageDisk().'/'.$fieldModel->value);
        }

        $fieldModel->value = "";
        $fieldModel->save();

        return response()->json( ["message"=>$fieldName], 200);

    }
}

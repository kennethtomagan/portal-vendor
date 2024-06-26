<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Laravel\Nova\Http\Requests\ResourceUpdateOrUpdateAttachedRequest;
use Laravel\Nova\Http\Requests\ResourceCreateOrAttachRequest;
use Laravel\Nova\Http\Resources\UpdateViewResource;
use Laravel\Nova\Http\Resources\CreateViewResource;

/*
|--------------------------------------------------------------------------
| Tool API Routes
|--------------------------------------------------------------------------
|
| Here is where you may register API routes for your tool. These routes
| are loaded by the ServiceProvider of your tool. They are protected
| by your tool's "Authorize" middleware by default. Now, go build!
|
*/

Route::get('{resource}/{resourceId}/{field}/new-fields', function(\Laravel\Nova\Http\Requests\NovaRequest $request, $resource, $resourceId, $fieldName){
    $resource = $request->newResource();

//    $method = $resourceId == "new" ? "updateFields" : "creationFields";
    $method = "creationFields";

    $theField = $resource->{$method}($request)->where("attribute", "=", $fieldName)->first() ?? false;

    if(!$theField){
        response()->json([
            "row" => []
        ], 404);
    }

    return response()->json([
        "row" => $theField->{$method}()
    ]);

});

Route::get('{resource}/new/{field}/new-fields', function(ResourceCreateOrAttachRequest $request, $resource, $fieldName){

    $resource = \Laravel\Nova\Http\Resources\CreateViewResource::make()->newResourceWith($request);
    $theField = $resource->creationFields($request)->where("attribute", "=", $fieldName)->first() ?? false;

    if(!$theField){
        response()->json([
            "row" => []
        ], 404);
    }

    return response()->json([
        "row" => $theField->getFilteredFields("new")
    ]);

});

//updateFields

//Route::get('resource/field/{key}', 'Ttc\GlobalConfig\GlobalConfig@deleteField');
//Route::delete('/resource/field/{key}', 'Ttc\GlobalConfig\GlobalConfig@deleteField');

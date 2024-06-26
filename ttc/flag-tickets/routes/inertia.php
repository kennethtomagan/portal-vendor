<?php

use Illuminate\Support\Facades\Route;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Fields\Trix;
use Laravel\Nova\Fields\Select;
/*
|--------------------------------------------------------------------------
| Tool Routes
|--------------------------------------------------------------------------
|
| Here is where you may register Inertia routes for your tool. These are
| loaded by the ServiceProvider of the tool. The routes are protected
| by your tool's "Authorize" middleware by default. Now - go build!
|
*/

Route::get('/{tenant_id}/{id}', function (NovaRequest $request, $tenantId, $id) {
    $fields =  [
        Select::make("Set As", "status")->options([
            'public' => 'Public',
            'private' => 'Private'
        ])->rules('required'),
        Trix::make("Message", "content")
            ->withFiles('public')
            ->stacked()
            ->fullWidth()
            ->rules('required'),
        ];
    $props = [
        'tenantId' => $tenantId,
        'id' => $id,
        'messageFields' => $fields,
        'auth' => auth()->user(),
    ];
    return inertia('FlagTickets', $props);
});

Route::get('/', function (NovaRequest $request) {
    return inertia('FlagTickets');
});

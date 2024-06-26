<?php

namespace Ttc\GlobalConfig;

use App\Traits\NovaMenuFilters;
use Illuminate\Http\Request;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Menu\MenuItem;
use Laravel\Nova\Menu\MenuSection;
use Laravel\Nova\Nova;
use Laravel\Nova\Tool;

class Email extends Tool
{
    use NovaMenuFilters;

    public function __construct()
    {
        parent::__construct();

        $this->addToMenu("Setup");
    }


    /**
     * Perform any tasks that need to happen when the tool is booted.
     *
     * @return void
     */
    public function boot()
    {

    }

    /**
     * Build the menu that renders the navigation links for the tool.
     *
     * @param  \Illuminate\Http\Request $request
     * @return mixed
     */
    public function menu(Request $request)
    {
        return MenuItem::make('Global Config Email')
            ->path('/global-config/email');
    }

    /** Create the inpiut fields for this page.
     * We create a dummy Nova resource so we can use all the field display functionality.
     * @param NovaRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUpdateFields(NovaRequest $request){

        //this is a dummy Nova resource so we can use all the field display functionality
        $GCResource = new Resources\GlobalConfigResource();

        $fields = $GCResource->updateFieldsWithinPanels($request, $GCResource)->applyDependsOnWithDefaultValues($request);

        $toReturn = [
            'panels' => $GCResource->availablePanelsForUpdate($request, $GCResource, $fields),
        ];

//        return response()->json( $toReturn, 200);
        return response()->json( [], 200);

    }
}

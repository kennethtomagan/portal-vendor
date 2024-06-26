<?php

namespace Ttc\GlobalConfig\Resources;

use App\Nova\Resource as Resource;
use App\Traits\NovaFieldHelpers;
use Eminiarts\Tabs\Tabs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\BooleanGroup;
use Laravel\Nova\Fields\Code;
use Laravel\Nova\Fields\Color;
use Laravel\Nova\Fields\Image;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;
use Ttc\Message\Message;
use Ttc\Laraberg\Laraberg;


class GlobalConfigResource extends Resource
{

    use NovaFieldHelpers;

    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \Ttc\GlobalConfig\Http\Model\GlobalConfig::class;
//    public static $model = "";

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'id';
    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id',
    ];

    public function __construct($resource = null)
    {
        parent::__construct($resource);

    }

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function fields(NovaRequest $request)
    {


        return [
            Panel::make("Panel Name", [
                Text::make("panel 1 text field", "panel_1_field")
                    ->rules('required', function($attribute, $value, $fail) {
                        if (strtoupper($value) !== $value) {
                            return $fail('The '.$attribute.' field must be uppercase.');
                        }
                        return true;
                    })
                    ->placeholder('My New Post')
                ,
                Laraberg::make("Page Content", "post_content")
//                ->withFiles('public')
                    ->stacked(),
//                Textarea::make("Laraberg", "laraberg")
//                    ->alwaysShow()
//                    ->withMeta(
//                        [
//                            'extraAttributes' => [
//                                'ref' => 'larabergField',
//                                'class' => 'larabergField',
//                            ]
//                        ]
//                    )
//                    ->stacked(),
                Number::make("panel 1 Number field", "panel_1_number_field")
                    ->rules('', function($attribute, $value, $fail) {
                        if ($value < 5) {
                            return $fail('The '.$attribute.' field must be higher than 5.');
                        }
                        if ($value > 7) {
                            return $fail('The '.$attribute.' field must be lower than 7.');
                        }
                        return true;
                    }),
                Boolean::make('Active'),
                BooleanGroup::make('Permissions')->options([
                    'create' => 'Create',
                    'read' => 'Read',
                    'update' => 'Update',
                    'delete' => 'Delete',
                ]),
                Color::make('Color', 'label_color'),
                Select::make('Size')->options([
                    'S' => 'Small',
                    'M' => 'Medium',
                    'L' => 'Large',
                ]),
            ]),
            Image::make('Photo', 'photo')
                ->disk(config("tenancy.filesystem.disks.public"))
                ->acceptedTypes('.jpg,.png')
                ->preview(function ($value, $disk) {
                    $field_row = $this->loadFirstFieldModel("photo");
                    $value = empty($value) ? $field_row->value : $value;
                    return $value
                        ? Storage::disk($disk)->url($value)
                        : null;
                })
                ->default(function ($request){
                    Log::debug($request);
                    return "tqeR5f7i5QTbtq5BLWOT1sncco3uJDKyJPGcpKB7.png";
                })
//                ->path($request->user()->id.'-attachments')
            ,
            Code::make("some Code", "singlecode"),
            Tabs::make('site-configuration', [
                'General'    => [
                    Message::make('Message')->html("<h3 class='px-4 py-4'>General Panel</h3>"),
                    Text::make("Tab 1 Text field", "tab_1_panel_field"),
                ],
                'User Permissions'    => [
                    Message::make('Message')->html("<h3>User Permissions</h3>"),
                    Text::make("Tab 2 Text field", "tab_2_panel_field"),
                ],
            ]),

        ];
    }

    /**
     * Get the cards available for the request.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function cards(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function filters(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function lenses(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function actions(NovaRequest $request)
    {
        return [];
    }
}

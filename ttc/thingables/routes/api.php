<?php

use App\Http\Resources\Shared\TicketMessageResource;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Faker\Factory as Faker;
use \App\Http\Controllers\Shared\LoadSharedMorphByModelController;
use Barryvdh\Debugbar\Facades\Debugbar;

/*
|--------------------------------------------------------------------------
| Tool API Routes
|--------------------------------------------------------------------------
|
| Here is where you may register API routes for your tool. These routes
| are loaded by the ServiceProvider of your tool. You're free to add
| as many additional routes to this file as your tool may require.
|
*/

 Route::post('/{resource}/{resourceId}/add-things', function (Request $request, $resourceName, $resourceId) {

     $parentResource = $request->input('parentResource');
     $thingsModel = $request->input('thingsModel');
     $message = $request->input('Message');
     $status = ucfirst($request->input('Set_As'));

     $theTicket = $parentResource::$model::find($resourceId);

     if(!class_exists($parentResource)){
         throw new Exception("Class ".$parentResource. "Doesnt exist");
     }

     $createMessageInfo = [
         "content" => $message,
         "type" => "new",
         "status" => $status,
         "owner_type" => get_class(auth()->user()),
         "owner_id" => auth()->user()->id,
     ];

     $theTicket->messages()->create($createMessageInfo);

//      Debugbar::info($theTicket->messages()->get());

     //Load all the messages
     $tmc = new LoadSharedMorphByModelController($theTicket, "messages");
     $messages = $tmc->resolve();

     //create our demo return message


//     $messages->push($demoContent);

     return ["messages" => $messages];

 });

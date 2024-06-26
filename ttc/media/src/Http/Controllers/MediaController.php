<?php
namespace Ttc\Media\Http\Controllers;

use App\Models\Shared\Media;
use Illuminate\Http\Request;

class MediaController
{
    public function index(Request $request)
    {
        $media = Media::all();

        $media = $media->map(function ($media) {
            $media->url = $media->getFullUrl();
            return $media;
        });
        
        return inertia('Media', ['media_items' => $media->toArray()]);
    }
}
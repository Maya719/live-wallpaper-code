<?php

namespace App\Http\Controllers\api;

use Illuminate\Support\Facades\Storage;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{
    event_category as event,
    Categories as ModelsCategories,
    LiveWallpapers_Panel
};

class LiveWallpaperController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $live_wallpapers = LiveWallpapers_Panel::with('category.events')->get()->map(function ($wallpaper) {
            $category = $wallpaper->category;

            return [
                'id' => (string) $wallpaper->id,
                'cat_id'=> (string) $wallpaper->cat_id,
                'cat_name' => $category ? $category->name : '',
                'thumbPath' => url(Storage::url($wallpaper->thumb_path)),
                'has_event' => $category && $category->events->isNotEmpty(),
            ];
        })->sortByDesc('has_event')
            ->values()
            ->map(function ($wallpaper) {
                unset($wallpaper['has_event']);
                return $wallpaper;
            });

        $grouped_wallpapers = $live_wallpapers->groupBy('cat_name');

        $response = [];

        $item_wallpapers = $grouped_wallpapers->map(function ($wallpapers) {
            return $wallpapers->first();
        })->values()->all();

        $response[] = [
            'viewType' => '1',
            'wallpapers' => $item_wallpapers,
        ];

        $categories = ModelsCategories::all();

        foreach ($categories as $category) {
            $category_wallpapers = LiveWallpapers_Panel::whereHas('category', function ($query) use ($category) {
                $query->where('name', $category->name);
            })->get()->map(function ($wallpaper) use ($category) {
                return [
                    'id' => (string) $wallpaper->id,
                    'blurPath' => url(Storage::url( $wallpaper->blur_path)),
                    'likes' => (string) $wallpaper->likes,
                    'downloads' => (string) $wallpaper->downloads,
                    'cat_name' => $category->name,
                    'cat_id' => (string) $wallpaper->cat_id,
                    'tags' => $wallpaper->hash_tags,
                    'thumbPath' => url(Storage::url( $wallpaper->thumb_path)),
                    'img_path' => url(Storage::url( $wallpaper->video_path)),
                ];
            });

            $category_wallpapers = $category_wallpapers->shuffle();

            if ($category_wallpapers->isNotEmpty()) {
                $response[] = [
                    'viewType' => '4',
                    'wallpapers' => $category_wallpapers,
                ];
            }
        }

        return response()->json([
            'response' => $response,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $single_cat_wlp = LiveWallpapers_Panel::where('cat_id', $id)->get()->map(function ($wallpaper) {
            return [
                'id' => (string) $wallpaper->id,
                'blurPath' => url(Storage::url( $wallpaper->blur_path)),
                'asset_type'=>'O',
                'likes' => (string) $wallpaper->likes,
                'downloads' => (string) $wallpaper->Downloads,
                'thumbPath' => url(Storage::url($wallpaper->thumb_path)),
                'img_path' => url(Storage::url($wallpaper->video_path)),
            ];
        });

        return response()->json($single_cat_wlp);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}

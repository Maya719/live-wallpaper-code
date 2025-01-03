<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mask extends Model
{
    use HasFactory;

    protected $fillable = ['wallpaper_id', 'mask_path'];

    public function wallpaper()
    {
        return $this->belongsTo(FourDwallpaper::class, 'wallpaper_id');
    }
}

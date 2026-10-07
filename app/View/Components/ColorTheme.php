<?php

namespace App\View\Components;

use App\Models\ColorPalette;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\Component;

class ColorTheme extends Component
{
    /**
     * Create a new component instance.
     */
    public $data;
    public $colorPalettes;

    public function __construct()
    {
        $this->data['frontend'] = \App\Models\ColorTheme::where('type', 0)->where('user_id', Auth::user()->id)->orderBy('id', 'desc')->get();

        $this->colorPalettes = ColorPalette::where('active_status', 1)->get();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        $data= $data= $this->data;
        $colorPalettes = $this->colorPalettes;
        return view('components.color-theme', compact('data', 'colorPalettes'));
    }
}

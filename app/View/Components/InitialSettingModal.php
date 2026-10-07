<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class InitialSettingModal extends Component
{
    /**
     * Create a new component instance.
     */
    public $setting;
    public function __construct()
    {
        $this->setting = shopSetting();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.initial-setting-modal');
    }
}

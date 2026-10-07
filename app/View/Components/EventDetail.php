<?php

namespace App\View\Components;

use Closure;
use App\Models\Event;
use Illuminate\View\Component;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;


class EventDetail extends Component
{
    /**
     * Create a new component instance.
     */
    public $recentEvent;

    public function __construct()
    {
        $this->recentEvent = Event::where('start_date', '>', now())
            ->where(function (Builder $query) {
                $userId = \Auth::user()->id;
                $query->where('user_id', $userId)
                    ->orWhereJsonContains('guest_user_ids', str($userId));
            })
            ->orderBy('start_date', "DESC")
            ->first();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        $recentEvent = $this->recentEvent;
        return view('components.event-detail', compact('recentEvent'));
    }
}

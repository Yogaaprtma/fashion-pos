<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class LoyaltyTier extends Component
{
    public int $points;
    public string $tierName;
    public string $color;
    public string $icon;

    /**
     * Create a new component instance.
     */
    public function __construct(int $points = 0)
    {
        $this->points = $points;
        $this->calculateTier();
    }

    private function calculateTier(): void
    {
        if ($this->points >= 5000) {
            $this->tierName = 'Gold';
            $this->color = '#F59E0B';
            $this->icon = '🥇';
        } elseif ($this->points >= 1000) {
            $this->tierName = 'Silver';
            $this->color = '#94A3B8';
            $this->icon = '🥈';
        } else {
            $this->tierName = 'Bronze';
            $this->color = '#CD7C2F';
            $this->icon = '🥉';
        }
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.loyalty-tier');
    }
}

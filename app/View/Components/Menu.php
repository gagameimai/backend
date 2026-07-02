<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Menu extends Component
{
    /**
     * @var string
     */
    public $guard;

    /**
     * Create a new component instance.
     *
     * @param string $guard
     * @return void
     */
    public function __construct($guard)
    {
        $this->guard = $guard;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.menu', [
            'menu' => config("menu.{$this->guard}"),
        ]);
    }
}

<?php

namespace App\View\Components;

use Illuminate\View\Component;

class UnitTitle extends Component
{
    /**
     * @var string
     */
    public $guard;

    /**
     * @var string
     */
    public $area;

    /**
     * @var string
     */
    public $unit;

    /**
     * @var string
     */
    public $depend;

    /**
     * Create a new component instance.
     *
     * @param string $guard
     * @param string $area
     * @param string $unit
     * @param string $depend
     * @return void
     */
    public function __construct($guard, $area, $unit = '', $depend = '')
    {
        $this->guard = $guard;
        $this->area = $area;
        $this->unit = $unit;
        $this->depend = $depend;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.title', [
            'title' => [
                'area' => config("menu.{$this->guard}.{$this->area}.name") ?? '',
                'unit' => !empty($this->unit) ? config("menu.{$this->guard}.{$this->area}.belongs.{$this->unit}.name") : '',
                'depend' => $this->depend
            ]
        ]);
    }
}

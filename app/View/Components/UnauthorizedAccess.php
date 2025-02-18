<?php

namespace App\View\Components;

use Illuminate\View\Component;

class UnauthorizedAccess extends Component
{
    public $roleName;

    /**
     * Create a new component instance.
     *
     * @param string $roleName
     * @return void
     */
    public function __construct($roleName = 'User')
    {
        $this->roleName = $roleName;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('components.unauthorized-access');
    }
}

<?php

namespace App\View\Components;

use Illuminate\View\Component;

class UnauthorizedAccess extends Component
{
    public $error_code;
    public $message;

    /**
     * Create a new component instance.
     *
     * @param string $error_code
     * @param string $message
     */
    public function __construct($error_code, $message )
    {
        $this->$error_code = $error_code;
        $this->message = $message;
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


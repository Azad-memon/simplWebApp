<?php
namespace App\View\Components;

use Illuminate\View\Component;

class StatusToggle extends Component
{
    public $id;
    public $status;
     public $url;

    public function __construct($id, $status, $url)
    {
        $this->id = $id;
        $this->status = $status;
        $this->url = $url;
    }

    public function render()
    {
        return view('components.status-toggle');
    }
}

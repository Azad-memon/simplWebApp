<?php

namespace App\View\Components;

use Illuminate\View\Component;

class ProductFlageToggle extends Component
{
    public $id;
    public $status;
     public $url;
    public $type;

    public function __construct($id, $status, $url,$type)
    {
        $this->id = $id;
        $this->status = $status;
        $this->url = $url;
        $this->type = $type;
    }
    public function render()
    {
        return view('components.product-flage-toggle');
    }
}

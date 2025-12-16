<?php
namespace App\View\Components;

use Illuminate\View\Component;

class ImageUpload extends Component
{
    public $id;
    public $name;
    public $value;

     public $is_banner;


    public function __construct($id = 'full', $name = 'full', $value = null, $is_banner = false)
    {
        $this->id = $id;
        $this->name = $name;
        $this->value = $value;
        $this->is_banner = $is_banner;
    }

    public function render()
    {
        return view('components.image-upload');
    }
}


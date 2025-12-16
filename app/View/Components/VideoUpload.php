<?php

namespace App\View\Components;

use Illuminate\View\Component;

class VideoUpload extends Component
{
    public $id;
    public $name;
    public $value;

    public function __construct($id = 'video', $name = 'video', $value = null)
    {
        $this->id = $id;
        $this->name = $name;
        $this->value = $value;
    }

    public function render()
    {
        return view('components.video-upload');
    }
}

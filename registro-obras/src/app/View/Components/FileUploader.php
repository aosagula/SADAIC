<?php

namespace App\View\Components;

use Illuminate\View\Component;

class FileUploader extends Component
{
    public $name;
    public $exts;
    public $required;

    public function __construct($name, $exts = "image/png, image/jpeg, application/pdf", $required = false)
    {
        $this->name = $name;
        $this->exts = $exts;
        $this->required = $required;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('components.file-uploader');
    }
}

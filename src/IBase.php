<?php

namespace Imagecrop\Mehmeteminsayim;

use Illuminate\Support\Facades\Http;

class IBase {
    protected $image;

    public function __construct(Image $image)
    {
        $this->image = $image;
    }

    public function load($pathToImage): void
    {
        $this->image->load($pathToImage);
    }
}

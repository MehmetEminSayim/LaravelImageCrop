<?php
namespace Imagecrop\Mehmeteminsayim\Controllers;

use Illuminate\Http\Request;
use Imagecrop\Mehmeteminsayim\Inspire;

class ImageCropController
{
    public function __invoke(Inspire $inspire) {
        $quote = $inspire->justDoIt();
        return view('inspire::index', compact('quote'));
    }
}

<?php
namespace Imagecrop\Mehmeteminsayim\Controllers;

use Illuminate\Http\Request;

class ImageCropController
{
    public function index() {
        return view('imagecrop::index');
    }

    public function upload(Request $request) {
        $request->validate([
            'image' => 'required|image|max:2048',
        ]);

        $image = $request->file('image');
        $filename = time() . '.' . $image->getClientOriginalExtension();

        $destinationPath = public_path('uploads');
        $image->move($destinationPath, $filename);

        return response()->json(["status"=> "success","filename"=>$filename]);
    }
}

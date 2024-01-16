<?php
namespace Imagecrop\Mehmeteminsayim\Controllers;

use Illuminate\Http\Request;
use Imagecrop\Mehmeteminsayim\Models\ImageCrop;

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
        $size = $image->getSize();
        $filename = time() . '.' . $image->getClientOriginalExtension();

        $destinationPath = public_path('uploads');
        $image->move($destinationPath, $filename);

        $image =  ImageCrop::create([
            "filename" => $filename,
            "ext" => $image->getClientOriginalExtension(),
            "folder" => "uploads",
            "size"=> $size
        ]);

        return response()->json(["status"=> "success","filename"=>$filename,"record" =>$image]);
    }
}

<?php
namespace Imagecrop\Mehmeteminsayim\Controllers;

use ArtisansWeb\ImageOptimize\Optimizer;
use Illuminate\Http\Request;
use Imagecrop\Mehmeteminsayim\Models\ImageCrop;


class ImageCropController
{
    public function index() {
        return view('imagecrop::index');
    }

    public function upload(Request $request) {
        try {
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

            if ($request->filled("width")){

            }


            if ($request->optimize == 1){
                $img = new Optimizer();
                $img->optimize($destinationPath.$filename);
            }

            return response()->json(["status"=> "success","filename"=>$filename,"record" =>$image]);
        }catch (\Exception $exception){
            return response()->json(["status"=> "error","message"=>$exception->getMessage()]);
        }
    }
}

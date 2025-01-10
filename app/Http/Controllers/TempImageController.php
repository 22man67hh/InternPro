<?php

namespace App\Http\Controllers;

use App\Models\TempImage;
use Illuminate\Http\Request;
use Validator;

class TempImageController extends Controller
{
    public function store(Request $request){
        $validator=Validator::make($request->all(), [
            'image'=>'required|image'
        ]);
        if($validator->fails()){
            return response()->json([
                'status'=>false,
                'message'=>"Error in message",
                'errors'=>$validator->errors()]);
        }
          // Upload Image here
    $image  =$request->image;
    $ext=$image->getClientOriginalExtension();
    $imageName=time().'.'.$ext;
    $tempImage=new TempImage();
    $tempImage->name = $imageName;
    $tempImage->save();


    //move Image
    $image->move(public_path('uploads/temp'), $imageName);
    return response()->json([
        'status'=>true,
        'message'=>"Image Upload Success",
        'image'=>[
            'id'=> $tempImage->id,
            'name'=> $tempImage->name,
       ] ]);
}
    }
  



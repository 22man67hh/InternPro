<?php

namespace App\Http\Controllers;

use App\Models\blog;
use App\Models\TempImage;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use Validator;

class BlogController extends Controller
{
    // this will return all blogs
   public function index(Request $request){
    $blogs=blog::orderBy("created_at","desc");

    if(!empty($request->keyword)){
$blogs=$blogs->where("title","like","%".$request->keyword."%");
    }
    $blogs=$blogs->get();
return response()->json([
    'status'=>true,
    'data'=>$blogs

]);
   }
   // single blog
public function show($id){
    $blog=blog::find($id);
    if($blog==null){
        return response()->json([
            'status'=>false,
            'message'=>'Blog Not found'
        ]);
    }
    $blog['date']=\Carbon\Carbon::parse($blog->created_at)->format(('d M,Y'));
    return response()->json([
        'status'=>true,
        'data'=>$blog
    ]);

}

// store
public function store(Request $request){
  $validator= Validator::make($request->all(), [

        "title"=> "required|min:10",
        "author"=>'required|min:3'
    ]);
    if($validator->fails()){
        return response()->json([
            'status'=> 'false',
            'message'=> 'please fix the error first',
            'errors'=> $validator->errors()
        ]);
    }
    $blog=new blog();
    $blog->title = $request->title;
    $blog->author=$request->author;
    $blog->description=$request->description;
    $blog->shortDesc=$request->shortDesc;
    $blog->save();
    //save image here
   $tempimage= TempImage::find($request->image_id);
   if($tempimage!=null){
    $imagext=explode('.',$tempimage->name);
    $ext=last($imagext);
    $imageName=time().'-'.$blog->id.'.'.$ext;
    $blog->image=$imageName;
    $blog->save();
    $sourcePath=public_path('uploads/temp/'.$tempimage->name);
    $desPath=public_path('uploads/blogs/'.$imageName);
    File::copy($sourcePath, $desPath);
   }
    return response()->json([
        'status'=> 'true',
        'message'=> 'Insereted successfully',
        'data'=> $blog
    ]);
}
// update
public function update($id,Request $request){
    $blog=blog::find($id);
    if($blog==null){
        return response()->json([
            'status'=> 'false',
            'message'=> 'Blog not found',
           
        ]);

    }
    $validator= Validator::make($request->all(), [

        "title"=> "required|min:10",
        "author"=>'required|min:3'
    ]);
    if($validator->fails()){
        return response()->json([
            'status'=> 'false',
            'message'=> 'please fix the error first',
            'errors'=> $validator->errors()
        ]);
    }
    $blog->title = $request->title;
    $blog->author=$request->author;
    $blog->description=$request->description;
    $blog->shortDesc=$request->shortDesc;
    $blog->save();
    //save image here
   $tempimage= TempImage::find($request->image_id);
   if($tempimage!=null){
File::delete(public_path('uploads/blogs/'.$blog->image));

    $imagext=explode('.',$tempimage->name);
    $ext=last($imagext);
    $imageName=time().'-'.$blog->id.'.'.$ext;
    $blog->image=$imageName;
    $blog->save();
    $sourcePath=public_path('uploads/temp/'.$tempimage->name);
    $desPath=public_path('uploads/blogs/'.$imageName);
    File::copy($sourcePath, $desPath);
   }
    return response()->json([
        'status'=> 'true',
        'message'=> 'Updated successfully',
        'data'=> $blog
    ]);
}
//delete
public function destroy($id){
   $blog= blog::find($id);
   if($blog ==null){
    return response()->json([
        'status'=>false,
        'message'=>'Blog Not Found',
    ]);}
    //delete image
    File::delete(public_path('uploads/blogs/'.$blog->image));
    $blog->delete();
    return response()->json([
        'status'=>true,
        'message'=>'Blog Deleted SuccessFully',
    ]);
}
}

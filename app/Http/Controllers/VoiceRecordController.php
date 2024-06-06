<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class VoiceRecordController extends Controller
{
    public function index()
    {
        $voices = DB::table('voices')->get();
        return view('index',['voices'=>$voices]);
    }

    
    public function save_temp(Request $request)
    {
        
        if ($request->hasFile('audio')) {
            $file = $request->file('audio');
            $path = $file->store('audios/temp', 'public');

            return response()->json(['path' => $path], 200);
        }

        return response()->json(['error' => 'No file uploaded'], 400);
    }



    public function store(Request $request)
    {
        
        if ($request->audio_path) {
            $path = $request->audio_path;
            $newPath = str_replace('audios/temp', "audios/voice", $path);
            $file = $request->audio_path;
            $name = rand(1,10).".mp3";
            Storage::putFileAs($newPath,$file,$name);
            DB::table('voices')->insert(['voice' => "$newPath/$name"]);
            Storage::deleteDirectory("audios/temp");
        }
        return redirect()->back();
    }
}

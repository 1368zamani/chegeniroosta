<?php

namespace App\Http\Controllers;

use App\Models\Village;
use App\Models\VillageImage;
use Illuminate\Http\Request;

class VillageImageController extends Controller
{

    public function create(Village $village)
    {
        return view('admin.images.create', compact('village'));
    }



    public function store(Request $request, Village $village)
    {

        $request->validate([
            'image' => 'required|image|max:2048',
            'title' => 'nullable|string',
            'description' => 'nullable|string',
        ]);



        $path = $request->file('image')
            ->store('villages', 'public');



        VillageImage::create([

            'village_id' => $village->id,

            'title' => $request->title,

            'description' => $request->description,

            'image' => $path,

        ]);



        return redirect()
            ->route('admin.dashboard')
            ->with('success','تصویر با موفقیت اضافه شد');

    }

}

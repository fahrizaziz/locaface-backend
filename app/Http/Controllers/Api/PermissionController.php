<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    //create
    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required',
            'reason' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048' // Pastikan validasi gambar ada
        ]);

        $permission = new Permission();
        $permission->user_id = $request->user()->id;
        $permission->date_permission = $request->date;
        $permission->reason = $request->reason;
        $permission->is_approved = 0;

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = $image->hashName(); // Generate nama file unik
            $image->move(public_path('permissions'), $imageName); // Simpan di public/permissions/

            $permission->image = 'permissions/' . $imageName; // Simpan hanya path ke database
        }

        $permission->save();

        return response()->json([
            'message' => 'Permission created successfully',
            'image_url' => $permission->image ? asset($permission->image) : null // Kirim URL gambar
        ], 201);
    }
}

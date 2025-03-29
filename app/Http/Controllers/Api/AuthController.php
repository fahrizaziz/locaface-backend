<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    //login
    public function login(Request $request)
    {
        $loginData = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $loginData['email'])->first();

        //check user exist
        if (!$user) {
            return response(['message' => 'Invalid credentials'], 401);
        }

        //check password
        if (!Hash::check($loginData['password'], $user->password)) {
            return response(['message' => 'Invalid credentials'], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response(['user' => $user, 'token' => $token], 200);
    }

    //logout
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response(['message' => 'Logged out'], 200);
    }

    //update image profile & face_embedding
    public function updateProfile(Request $request)
    {
        try {
            // Validasi request
            $request->validate([
                // 'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
                'face_embedding' => 'required',
            ]);

            $user = $request->user();
            // $image = $request->file('image');
            $face_embedding = $request->face_embedding;

            // Simpan gambar ke public/images/
            // $imageName = $image->hashName(); // Ambil nama hash unik
            // $image->move(public_path('images'), $imageName);

            // Simpan hanya path tujuan di database
            // $user->image_url = 'images/' . $imageName;
            $user->face_embedding = $face_embedding;
            $user->save();

            return response()->json([
                'message' => 'Profile updated',
                'user' => $user,
                'image_url' => asset($user->image_url) // URL lengkap gambar
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update profile',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    //update fcm token
    public function updateFcmToken(Request $request)
    {
        $request->validate([
            'fcm_token' => 'required',
        ]);

        $user = $request->user();
        $user->fcm_token = $request->fcm_token;
        $user->save();

        return response([
            'message' => 'FCM token updated',
        ], 200);
    }
}

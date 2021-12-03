<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\photouser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'role' => $request->input('role'),
            'password' => Hash::make($request->input('password'))
        ]);

        return $user;
    }

    public function login(Request $request)
    {
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response([
                'message' => 'Invalid credentials'
            ], Response::HTTP_UNAUTHORIZED);
        }

        $user = Auth::user();

        $token = $user->createToken('token')->plainTextToken;

        $cookie = cookie('jwt', $token, 60 * 24); // 1 day

        return response([
            'message' => 'Success',
            'role' => $user->role,
            'token' => $token
        ])->withCookie($cookie);
    }

    public function user()
    {
        $user = Auth::user();
        if ($user->role == 1) {
            return response([
                'message' => 'Welcome Admin',
                'profile' => $user
            ]);
        } elseif ($user->role == 0) {
            return response([
                'message' => 'Welcome Customer',
                'profile' => $user
            ]);
        }
    }

    public function logout()
    {
        $cookie = Cookie::forget('jwt');
        return response([
            'message' => 'Success'
        ])->withCookie($cookie);
    }

    public function get_all_user()
    {
        if (Auth::user()->role == 1) {
            return response(['message' => 'Success get all Users', 'users' => User::all()]);
        }
    }
    public function get_user_by_id(Request $request)
    {
        if (Auth::user()->role == 1) {
            return response(['message' => 'Success get Users', 'users' => User::find($request->id_user)]);
        }
    }

    public function download_profilePicture(Request $request)
    {
        $file_name = photouser::find($request->id_user);
        return response()->download(public_path($file_name->path), "User Image");
    }

    public function upload_profilePicture(Request $request)
    {
        $path = $request->file('photo')->move(public_path('/photouser' . '/'), $request->file_name);
        $photoURL = url('/photouser' . '/' . $request->file_name);

        $photo = photouser::create([
            'id_user' => $request->id_user,
            'path' => $request->file_name
        ]);

        return  response(['message' => 'Success upload image', 'photo' => $photo])->json(['url' => $photoURL], 200);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\photouser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;
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
        $list_picture = array();
        $photo = photouser::where('id_user', $user->id)->get();
        $photoURL = url('/photouser' . '/' . $photo);
        array_push($list_picture, ['id_picture' => $photo->id, 'url' => $photoURL]);
        $user->picture = $list_picture;
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
            $user = User::find($request->id_user);
            $list_picture = array();
            $photo = photouser::where('id_user', $user->id)->get();
            $photoURL = url('/photouser' . '/' . $photo);
            array_push($list_picture, ['id_picture' => $photo->id, 'url' => $photoURL]);
            $user->picture = $list_picture;
            return response(['message' => 'Success get Users', 'users' => $user]);
        }
    }

    public function download_userPicture(Request $request)
    {
        $file_name = photouser::where('id_user', $request->id_user)->get();

        $list_picture = array();

        foreach ($file_name as $value) {
            if (file_exists(public_path('photouser/' . $value->path))) {
                $user_picture = $value->path;
                $photoURL = url('/photouser' . '/' . $user_picture);
                array_push($list_picture, ['id_picture' => $value->id, 'url' => $photoURL]);
            } else {
                $photo = photouser::find($value->id);
                $photo->delete();
            }
        }

        return response([
            'message' => 'Success get all picture for this user',
            'list_picture' => $list_picture
        ]);
    }

    public function upload_userPicture(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $imageName = $request->image->getClientOriginalName();

        $imageName = preg_replace('/\s+/', '_', $imageName);


        $i = true;
        $j = 0;
        while ($i == true) {
            $picture = photouser::where('path', $imageName)->count();
            if ($picture > 0) {
                $j++;
                $imageName = basename(
                    $request->image->getClientOriginalName(),
                    '.' . $request->image->getClientOriginalExtension()
                )
                    . ' ' . ($picture + $j) . '.' . $request->image->getClientOriginalExtension();

                $imageName = preg_replace('/\s+/', '_', $imageName);
            } else {
                $i = false;
            }
        }

        $request->image->move(public_path('photouser'), $imageName);

        $photo = photouser::create([
            'id_user' => $request->id_user,
            'path' => $imageName
        ]);

        $photo->save();

        $photoURL = url('/photouser' . '/' . $imageName);

        return response(['fileName' => $imageName, 'url' => $photoURL]);
    }

    public function delete_userPicture(Request $request)
    {
        $photo = photouser::find($request->id_picture)->first();

        File::delete(public_path('photouser/' . $photo->path));

        $photo->delete();

        return response(['message' => 'Success deleting picture']);
    }
}

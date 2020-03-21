<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ApiTokenController extends Controller
{
    public function update(Request $request)
    {
        $token = Str::random(80);

        $request->user()->forceFill([
            'api_token' => hash('sha256', $token),
        ])->save();

        return ['token' => $token];
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'senha' => 'required',
        ]);

        $user = User::where('email', $request['email'])->orWhere('username', $request['email'])->first();

        if ($user && Hash::check($request['senha'], $user->password)) {
            $token = Str::random(80);
            $user->forceFill([
                'api_token' => hash('sha256', $token),
            ])->save();

            return [
                'status' => 1,
                'token' => $token,
            ];
        }

        return [
            'status' => 0,
        ];
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class SSOController extends Controller
{
    public function redirectToExpress()
    {
        // 1. Pastikan user sudah login di Laravel Dashboard
        $user = auth()->user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        // 2. Generate token JWT berbasis data user yang sedang login
        $token = JWTAuth::fromUser($user);

        // 3. Alihkan (redirect) user ke url callback Express Anda (port 3002)
        $expressUrl = "http://31.97.199.58:3002/sso/callback";

        return redirect($expressUrl . '?token=' . $token);
    }
}

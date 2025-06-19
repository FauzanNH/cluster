<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PingApiController extends Controller
{
    public function ping()
    {
        return response()->json([
            'success' => true,
            'message' => 'API Connected'
        ]);
    }

    public function cekAkun(Request $request)
    {
        $request->validate([
            'email' => 'required_without:users_id|email',
            'users_id' => 'required_without:email',
        ]);

        $query = \App\Models\User::query();
        if ($request->has('email')) {
            $query->where('email', $request->email);
        }
        if ($request->has('users_id')) {
            $query->where('users_id', $request->users_id);
        }
        $user = $query->first();

        if ($user) {
            return response()->json([
                'success' => true,
                'message' => 'Akun ditemukan',
                'user' => [
                    'users_id' => $user->users_id,
                    'role' => $user->role,
                ]
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Akun tidak ditemukan.'
            ], 404);
        }
    }
}

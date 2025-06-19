<?php

namespace App\Http\Controllers\Api\warga;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class LainnyaApiController extends Controller
{
    public function userinfo(Request $request)
    {
        $users_id = $request->input('users_id');
        if (!$users_id) {
            return response()->json(['error' => 'users_id is required'], 400);
        }

        $user = User::find($users_id);
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        return response()->json([
            'name' => $user->nama,
            'created_at' => $user->created_at->format('d M Y'),
        ]);
    }
}

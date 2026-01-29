<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
class UserController extends Controller
{
    
    public function userCount(){
        $totalUsers = User::count();

        return response()->json([
            'totalUsers' => $totalUsers
        ]);
    }
}

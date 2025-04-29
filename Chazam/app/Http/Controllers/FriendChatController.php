<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FriendChatController extends Controller
{
    public function index()
    {
        return view('user.friendchat');
    }
} 
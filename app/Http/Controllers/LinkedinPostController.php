<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LinkedinPostController extends Controller
{
     public function create()
    {
        return view('linkedin_post');
    }
}

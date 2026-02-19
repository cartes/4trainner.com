<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PageController extends Controller
{
    /**
     * Display the pricing page.
     */
    public function pricing()
    {
        return view('pages.pricing');
    }
}

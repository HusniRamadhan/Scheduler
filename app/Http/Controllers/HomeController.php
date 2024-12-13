<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return RedirectResponse|Redirector
     */
    public function index(): RedirectResponse|Redirector
    {
        if (Auth::user()->role == 'admin' || Auth::user()->role == 'dosen') {
            return redirect('admin');
        }

        if (Auth::user()->role == 'member') {
            return redirect('user');
        }

        return redirect('default-route'); // Add a fallback redirect if no condition is met
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Term;
use Illuminate\Http\Request;

class TermController extends Controller
{
    /**
     *  index
     */
    public function index()
    {

        $term = Term::first();

        return view('terms.index', compact('term'));
    }
}

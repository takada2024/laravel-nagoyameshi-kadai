<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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

        return view('admin.terms.index', compact('term'));
    }

    /**
     *  edit
     */
    public function edit(Term $term)
    {
        return view('admin.terms.edit', compact('term'));
    }

    /**
     *  update
     */
    public function update(Request $request, Term $term)
    {
        $request->validate([
            'content' => 'required',
        ]);

        $term->content = $request->input('content');
        $term->save();

        return redirect()->route('admin.terms.index')->with('flash_message', '利用規約を編集しました。');
    }
}

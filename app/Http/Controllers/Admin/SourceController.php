<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SourceList;

class SourceController extends Controller
{
    public $prefix_route = 'admin.sources.';
    public function index()
    {
        $sources = SourceList::orderBy('created_at', 'desc')->paginate(10);

        return view($this->prefix_route . 'index', get_defined_vars());
    }

    public function getForm(Request $request)
    {
        $source = null;
        $route = route($this->prefix_route . 'store');
        if ($request->id) {
            $source = SourceList::where('id', $request->id)->first();
            $route = route($this->prefix_route . 'store', $request->id);
        }

        $view = view($this->prefix_route . 'components.form', get_defined_vars())->render();
        return response()->json(['view' => $view]);
    }

    public function store(Request $request, $id = null)
    {
        $request['load_once'] = $request->load_once ?? 0;

        $s = SourceList::updateOrCreate(['id' => $id], $request->all());

        return response()->json(['success' => 'successfully submited', 'reload' => true]);
    }

    public function delete($id)
    {
        SourceList::destroy($id);

        return redirect()->back()->with('message', 'Successfully deleted');
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DropdownMatchable;
use App\Http\Requests\Admin\MatchableRequest;

class DropdownMatchableController extends Controller
{
    public $prefix_route = 'admin.dropdown.matchables.';
    public function index()
    {
        $items = DropdownMatchable::orderBy('created_at', 'desc')->paginate(10);

        return view($this->prefix_route . 'index', get_defined_vars());
    }

    public function getForm(Request $request)
    {
        $item = null;
        $route = route($this->prefix_route . 'store');
        if ($request->id) {
            $item = DropdownMatchable::where('id', $request->id)->first();
            $route = route($this->prefix_route . 'store', $request->id);
        }

        $view = view($this->prefix_route . 'components.form', get_defined_vars())->render();
        return response()->json(['view' => $view]);
    }

    public function store(MatchableRequest $request, $id = null)
    {
        $options = [];
        foreach ($request['options']['keys'] as $index => $key) {
            $options[] = [
                'key' => $key,
                'value' => $request['options']['values'][$index] ?? null
            ];
        }

        $s = DropdownMatchable::updateOrCreate(['table' => $request->table, 'column' => $request->column], ['content' => json_encode($options)]);

        return response()->json(['success' => 'successfully submited', 'reload' => true]);
    }

    public function delete($id)
    {
        DropdownMatchable::destroy($id);

        return redirect()->back()->with('message', 'Successfully deleted');
    }
}

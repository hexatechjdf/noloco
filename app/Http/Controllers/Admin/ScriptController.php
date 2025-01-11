<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Script;
use App\Http\Requests\Admin\ScriptRequest;

class ScriptController extends Controller
{

    public $prefix_route = 'admin.scripts.';
    public function index()
    {
        $scripts = Script::orderBy('created_at', 'desc')->paginate(10);

        return view($this->prefix_route . 'index', get_defined_vars());
    }

    public function getForm(Request $request)
    {
        $script = null;
        $route = route($this->prefix_route . 'store');
        if ($request->id) {
            $script = Script::where('id', $request->id)->first();
            $route = route($this->prefix_route . 'store', $request->id);
        }

        $view = view($this->prefix_route . 'components.form', get_defined_vars())->render();
        return response()->json(['view' => $view]);
    }

    public function store(ScriptRequest $request, $id = null)
    {
        $request['load_once'] = $request->load_once ?? 0;

        $s = Script::updateOrCreate(['id' => $id], $request->validated());

        return response()->json(['success' => 'successfully submited', 'reload' => true]);
    }

    public function delete($id)
    {
        Script::destroy($id);

        return redirect()->back()->with('message', 'Successfully deleted');
    }


}

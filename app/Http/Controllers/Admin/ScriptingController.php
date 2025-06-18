<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Scripting;
use Illuminate\Support\Facades\Storage;

class ScriptingController extends Controller
{
    public function index()
    {
        $scriptings = Scripting::paginate(10);
        return view('admin.scriptings.index',get_defined_vars());
    }

    public function setting($id= null)
    {
        $selectedLocations = [];
        $scripting = null;
        if($id)
        {
           $scripting = Scripting::where('id',$id)->first();
           $selectedLocations = json_decode($scripting->locations ?? '',true);
        }

        return view('admin.scriptings.create',get_defined_vars());
    }

    public function store(Request $request, $id= null)
    {
        $request['locations'] = json_encode($request->locations ?? []);
        $s = Scripting::updateOrCreate(['id' => $id], $request->all());

        $c = $id ? $s->uuid :  $this->generateUniqueCode();


        $cssFileName = $s->title. '-'.$s->id.$c . '.css';
        $jsFileName  =  $s->title. '-'.$s->id.$c . '.js';

        // Store the files
        storeFiles('styles',$cssFileName,$request['css'] ?? '');
        storeFiles('scripts',$jsFileName,$request['js'] ?? '');

        $s->uuid = $c;
        $s->css_link = asset('styles/' . $cssFileName);
        $s->js_link = asset('scripts/' . $jsFileName);
        $s->save();

       return response()->json(['success' => 'success']);
    }

    public function delete($id)
    {
        Scripting::destroy($id);

        return redirect()->back()->with('message', 'Successfully deleted');
    }

    private function generateUniqueCode(): string
    {
        do {
            $code = mt_rand(10000000, 99999999); // 8-digit number
        } while (Scripting::where('uuid', $code)->exists());

        return (string)$code;
    }
}

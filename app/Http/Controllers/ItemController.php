<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Items;
use Auth;


class ItemController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $items = Items::all()->toArray();
		return view('items.index', compact('items'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('items.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
		if(!Auth::user())
		{
			return back()->withErrors(['Missing a required permission to create items']);
		}
		
        // form validation
		$item = $this->validate(request(), [
			'title' => 'required',
		]);
		
			//Handles the uploading of the image
			if ($request->hasFile('image')){
				//Gets the filename with the extension
				$fileNameWithExt = $request->file('image')->getClientOriginalName();
				//just gets the filename
				$filename = pathinfo($fileNameWithExt, PATHINFO_FILENAME);
				//Just gets the extension
				$extension = $request->file('image')->getClientOriginalExtension();
				//Gets thefilename to store
				$fileNameToStore = $filename.'_'.time().'.'.$extension;
				//Uploads the image
				$path =$request->file('image')->storeAs('public/images', $fileNameToStore);
				}
				
			else {
				$fileNameToStore = 'noimage.jpg';
			}
				
		// create a item object and set its values from the input
		$item = new items;
		$item->title = $request->input('title');
		$item->description = $request->input('description');
		//$item->found_userid = auth()->user()->id;
        $item->found_time = $request->input('found_time');
        $item->location = $request->input('location');
        $item->color = $request->input('color');
		$item->category = $request->input('category');
		$item->created_at = now();$item->image = $fileNameToStore;
		
		// save the item object
		$item->save();
		// generate a redirect HTTP response with a success message
		return back()->with('success', 'item has been added'); 
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
		if(!Auth::user())
		{
			return back()->withErrors(['Missing a required permission to show items']);
		}
        $item = items::find($id);
		return view('items.show',compact('item'));

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $item = items::find($id);
		return view('items.edit',compact('item'));

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
		if(Gate::denies('admin'))
        {
            return back()->withErrors(['Missing a required permission to edit item']);
        }
        $item = items::find($id);
$this->validate(request(), [
'title' => 'required',
'category' => 'required',
'location' => 'required',
//'color' => 'color',
]);
		$item->title = $request->input('title');
        $item->category = $request->input('category');
        $item->found_time = $request->input('found_time');
        $item->location = $request->input('location');
        $item->color = $request->input('color');
        $item->description = $request->input('description');
        $item->updated_at = now();
		
//Handles the uploading of the image
if ($request->hasFile('image')){
//Gets the filename with the extension
$fileNameWithExt = $request->file('image')->getClientOriginalName();
//just gets the filename
$filename = pathinfo($fileNameWithExt, PATHINFO_FILENAME);
//Just gets the extension
$extension = $request->file('image')->getClientOriginalExtension();
//Gets the filename to store
$fileNameToStore = $filename.'_'.time().'.'.$extension;
//Uploads the image
$path = $request->file('image')->storeAs('public/images', $fileNameToStore);
} else {
$fileNameToStore = 'noimage.jpg';
}
$item->image = $fileNameToStore;
$item->save();
return redirect('items')->with('success','item has been updated');
}

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $item = items::find($id);
		$item->delete();
		return redirect('items')->with('success','item has been deleted');
    }
}

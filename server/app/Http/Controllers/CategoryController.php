<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Exports\CategoryExport;
use App\Imports\CategoryImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Requests\CategoryRequest;
use App\Http\Requests\ImportRequest;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Category::select('id', 'name');
        if ($request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        $categories = $query->get();
        return response()->json($categories);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CategoryRequest $request)
    {
        $category = Category::create([
            'name' => $request->name
        ]);

        return response()->json([
            "message" => "success",
            "data" => $category
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Category $category)
    {
        return response([
            "message" => "success",
            "data" => $category
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CategoryRequest $request, Category $category)
    {
        $category->update([
            'name' => $request->name
        ]);
        return response([
            "message" => "success",
            "data" => $category
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Category $category)
    {
        $category->delete();
        return response([
            "message" => "Deleted Successfully!",
            "data" => $category
        ]);
    }

    public function export(Request $request)
    {
        return Excel::download(new CategoryExport($request->keyword), 'categories.xlsx');
    }

    public function import(ImportRequest $request)
    {
        Excel::import(new CategoryImport, request()->file('file'));

        return response(200);
    }
}

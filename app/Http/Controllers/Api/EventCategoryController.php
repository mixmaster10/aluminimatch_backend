<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\EventCategoryRequest;
use App\Models\EventCategory;

class EventCategoryController extends Controller
{
    //
    public function index() {
        $category = EventCategory::with('subCategories')->whereNull('parent_id')->get();
        return response()->json($category);
    }

    public function show(EventCategory $category) {
        $category->subCategories;
        return response()->json($category);
    }

    public function store(EventCategoryRequest $request) {
        $category_info = $request->validated();
        EventCategory::create($category_info);
        $category = EventCategory::with('subCategories')->whereNull('parent_id')->get();
        return response()->json($category);
    }

    public function update(Request $request, EventCategory $category) {
        $category->update($request->all());
        $category = EventCategory::with('subCategories')->whereNull('parent_id')->get();
        return response()->json($category);
    }
}

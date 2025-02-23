<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Article;

class ArticleController extends Controller
{
    public function index(Request $request)
    {
        $query = Article::query();
        if ($request->has('source_site')) {
            $sourceSites = explode(',', $request->source_site); // Convert comma-separated values to array
            $query->whereIn('source_site', $sourceSites);
        }

        if ($request->has('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        if ($request->has('source')) {
            $query->where('source', $request->source);
        }

        if ($request->has('date')) {
            $query->whereDate('published_at', $request->date);
        }

        return response()->json($query->get());
    }
}

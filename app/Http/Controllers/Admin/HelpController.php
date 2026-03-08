<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\HelpService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Handles the help documentation section in the admin panel.
 */
class HelpController extends Controller
{
    /**
     * Display the help center index.
     */
    public function index(HelpService $helpService): Response
    {
        return Inertia::render('Admin/Help/Index', [
            'categories' => $helpService->getAllCategoriesWithArticles(),
            'tableOfContents' => $helpService->getTableOfContents(),
        ]);
    }

    /**
     * Display a specific help article.
     */
    public function show(string $slug, HelpService $helpService): Response
    {
        $article = $helpService->getArticleBySlug($slug);

        if (!$article) {
            abort(404);
        }

        return Inertia::render('Admin/Help/Show', [
            'article' => $article,
            'category' => $article->category,
            'tableOfContents' => $helpService->getTableOfContents(),
        ]);
    }

    /**
     * Display articles in a specific category.
     */
    public function category(string $slug, HelpService $helpService): Response
    {
        $category = $helpService->getCategoryBySlug($slug);

        if (!$category) {
            abort(404);
        }

        return Inertia::render('Admin/Help/Category', [
            'category' => $category,
            'articles' => $category->publishedArticles,
            'tableOfContents' => $helpService->getTableOfContents(),
        ]);
    }

    /**
     * Search help articles.
     */
    public function search(Request $request, HelpService $helpService): Response
    {
        $query = $request->input('q', '');
        $results = [];

        if (strlen($query) >= 2) {
            $results = $helpService->searchArticles($query);
        }

        return Inertia::render('Admin/Help/Search', [
            'query' => $query,
            'results' => $results,
            'tableOfContents' => $helpService->getTableOfContents(),
        ]);
    }
}

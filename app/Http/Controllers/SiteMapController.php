<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SiteMapController extends Controller
{
    // index
    public function index()
    {
        $frontend = config('app.frontend_url', env('FRONTEND_URL'));

    // All sitemap files
    $files = glob(public_path('sitemap.xml'));

    $sitemaps = [];

    foreach ($files as $file) {
        $content = simplexml_load_file($file); // Read XML content
        $urlList = [];

        // If normal sitemap <urlset>
        if (isset($content->url)) {
            foreach ($content->url as $u) {
                $urlList[] = (string) $u->loc;
            }
        }

        // If sitemap index <sitemapindex>
        if (isset($content->sitemap)) {
            foreach ($content->sitemap as $u) {
                $urlList[] = (string) $u->loc;
            }
        }

        $sitemaps[] = [
            'file' => basename($file),
            'url' => $frontend . '/' . basename($file),
            'items' => $urlList
        ];
    }

    return view('sitemap.index', compact('sitemaps'));
    }

    public function sync($file)
    {
        $frontend = config('app.frontend_url', env('FRONTEND_URL'));

        $sitemapUrl = $frontend . '/' . $file;

        $pingUrl = "https://www.google.com/ping?sitemap=" . urlencode($sitemapUrl);

        $result = file_get_contents($pingUrl);

        return back()->with('success', $file . ' synced to Google successfully!');
    }

    public function regenerate()
    {
        \Artisan::call('sitemap:generate');

        return back()->with('success', 'Sitemap regenerated successfully!');
    }
}

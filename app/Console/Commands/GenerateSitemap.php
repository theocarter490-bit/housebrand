<?php

namespace App\Console\Commands;

use App\Models\BlogPost;
use App\Models\Product;
use Illuminate\Console\Command;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;
use Spatie\Sitemap\SitemapIndex;
use Illuminate\Support\Facades\Storage;

class GenerateSitemap extends Command
{
    protected $signature = 'sitemap:generate';
    protected $description = 'Generate the sitemap.xml file';

    public function handle()
    {
        $frontend = rtrim(config('app.frontend_url', env('APP_FRONTEND_URL')), '/');
        $publicPath = public_path();
        $timestamp = now()->format('YmdHis');

        $sitemapFiles = [];
        $maxPerFile = 45000; // Google allows 50k, keep buffer
        $currentCount = 0;
        $currentFileIndex = 1;

        $createSitemap = function () use (&$sitemapFiles, &$currentFileIndex, $publicPath, $timestamp) {
            $file = "sitemap-{$timestamp}-{$currentFileIndex}.xml";
            $sitemapFiles[] = $file;
            $currentFileIndex++;
            return Sitemap::create();
        };

        $sitemap = $createSitemap();

        $addUrl = function ($url) use (&$sitemap, &$currentCount, $maxPerFile, $createSitemap, $frontend, &$sitemapFiles, $publicPath) {
            $sitemap->add($url);
            $currentCount++;

            if ($currentCount >= $maxPerFile) {
                // Write current sitemap & start new one
                $lastFile = last($sitemapFiles);
                $sitemap->writeToFile(public_path($lastFile));
                $sitemap = $createSitemap();
                $currentCount = 0;
            }
        };

        // ✴️ Static Pages
        collect([
            '/',
            '/about',
            '/contact',
            '/blog',
            '/designers',
            '/inspiration',
            '/plans',
            '/portfolio',
            '/product',
        ])->each(function ($page) use ($addUrl, $frontend) {
            $addUrl(
                Url::create("{$frontend}{$page}")
                    ->setPriority(0.9)
                    ->setChangeFrequency('weekly')
            );
        });

        // ✴️ Blog Posts
        BlogPost::where('publish_status', 1)
            ->whereHas('category', fn($q) => $q->where('is_active', 1))
            ->orderBy('id')
            ->chunk(1000, function ($posts) use ($addUrl, $frontend) {
                $posts->each(function ($post) use ($addUrl, $frontend) {
                    $addUrl(
                        Url::create("{$frontend}/blog/{$post->slug}")
                            ->setLastModificationDate($post->updated_at)
                            ->setPriority(0.8)
                    );
                });
            });

        // ✴️ Products
        Product::where('is_published', 1)
            ->whereHas('category', fn($q) => $q->where('active_status', 1))
            ->where(function ($q) {
                $q->whereNull('brand_id')
                    ->orWhereHas('brand', fn($b) => $b->where('active_status', 1));
            })
            ->whereHas('user', function ($q) {
                $q->where('active_status', 1)
                    ->where(function ($q2) {
                        $q2->where('subscription_required', 0)
                            ->orWhere(fn($x) => $x->where('subscription_required', 1)->where('is_subscribed', 1));
                    });
            })
            ->orderBy('id')
            ->chunk(2000, function ($products) use ($addUrl, $frontend) {
                $products->each(function ($product) use ($addUrl, $frontend) {

                    $shopSlug = optional($product->shop)->slug;
                    $slug     = $product->slug ?: 'product';
                    $id       = $product->id;

                    $path = $shopSlug
                        ? "/designer/{$shopSlug}/product/{$id}-{$slug}"
                        : "/product/{$id}-{$slug}";

                    $addUrl(
                        Url::create("{$frontend}{$path}")
                            ->setLastModificationDate($product->updated_at)
                            ->setChangeFrequency('weekly')
                            ->setPriority(0.7)
                    );
                });
            });

        // Write the last sitemap file
        $lastFile = last($sitemapFiles);
        if (isset($lastFile)) {
            $sitemap->writeToFile(public_path($lastFile));
        }

        // ✴️ Create sitemap index if more than one
        if (count($sitemapFiles) > 1) {
            $index = SitemapIndex::create();
            foreach ($sitemapFiles as $file) {
                $index->add(Url::create("{$frontend}/{$file}"));
            }
            $index->writeToFile(public_path('sitemap.xml'));
        } else {
            rename(public_path($lastFile), public_path('sitemap.xml'));
        }

        $this->info("✔ Sitemap generated successfully!");
    }
}

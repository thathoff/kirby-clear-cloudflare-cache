<?php

namespace TheStreamable\ClearCloudflareCache;

use Kirby\Cms\Page;
use Kirby\Http\Remote;
use Kirby\Toolkit\Collection;

class CloudflareCache
{
    protected const API_URL_BATCH_SIZE = 30;
    
    public static function handlePageHook($hook, $page, $oldPage = null)
    {
        $callback = option('thestreamable.clearcloudflarecache.dependantUrlsForPage');
        if ($callback && is_callable($callback)) {
            static::purgeURLs($callback($hook, $page, $oldPage));
        }
    }
    
    public static function handleFileHook($hook, $file, $oldFile = null)
    {
        $callback = option('thestreamable.clearcloudflarecache.dependantUrlsForFile');
        if ($callback && is_callable($callback)) {
            static::purgeURLs($callback($hook, $file, $oldFile));
        }
    }
    
    public static function handleSiteHook($hook, $site, $oldSite = null)
    {
        $callback = option('thestreamable.clearcloudflarecache.dependantUrlsForSite');
        if ($callback && is_callable($callback)) {
            static::purgeURLs($callback($hook, $site, $oldSite));
        }
    }
    
    public static function purgeURLs($pagesOrURLs)
    {
        if (!$pagesOrURLs) {
            return;
        }
        
        $cloudflareZone = option('thestreamable.clearcloudflarecache.cloudflareZoneID');
        $cloudflareEmail = option('thestreamable.clearcloudflarecache.cloudflareEmail');
        $cloudflareAPIKey = option('thestreamable.clearcloudflarecache.cloudflareAPIKey');
        if ('' == $cloudflareZone || '' == $cloudflareEmail || '' == $cloudflareAPIKey) {
            return;
        }
        
        if ($pagesOrURLs instanceof Collection) {
            $pagesOrURLs = $pagesOrURLs->pluck('url');
        }
        elseif ($pagesOrURLs instanceof Page) {
            $pagesOrURLs = [$pagesOrURLs->url()];
        }
        elseif (!is_array($pagesOrURLs)) {
            $pagesOrURLs = [$pagesOrURLs];
        }
        
        $pagesOrURLs = array_map(function($urlItem) {
            return $urlItem instanceof Page ? $urlItem->url() : (string)$urlItem;
        }, $pagesOrURLs);
        
        $pagesOrURLs = array_unique($pagesOrURLs);
        if (!count($pagesOrURLs)) {
            return;
        }
        
        foreach (array_chunk($pagesOrURLs, static::API_URL_BATCH_SIZE) as $urlBatch) {
            Remote::post('https://api.cloudflare.com/client/v4/zones/' . $cloudflareZone . '/purge_cache', [
                'headers' => [
                    'X-Auth-Email: ' . $cloudflareEmail,
                    'X-Auth-Key: ' . $cloudflareAPIKey,
                    'Content-Type: application/json',
                ],
                'data' => json_encode([
                    'files' => array_values($urlBatch),
                ]),
            ]);
        }
    }
    
}

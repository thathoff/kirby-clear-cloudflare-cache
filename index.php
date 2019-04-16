<?php

load([
    'TheStreamable\\ClearCloudflareCache\\CloudflareCache' => 'src/CloudflareCache.php'
], __DIR__);

use TheStreamable\ClearCloudflareCache\CloudflareCache;

Kirby::plugin('thestreamable/clearcloudflarecache', [
    'options' => [
        'cloudflareZoneID'      => null,
        'cloudflareEmail'       => null,
        'cloudflareAPIKey'      => null,
        'dependantUrlsForPage'  => function ($hook, $page, $oldPage = null) {
            return $oldPage ? [$page->url(), $oldPage->url()] : $page->url();
        },
        'dependantUrlsForSite'  => function ($hook, $site, $oldSite = null) {
            return $site->url();
        },
    ],
    'hooks' => [
        'page.changeNum:after' => function ($page, $oldPage) {
            CloudflareCache::handlePageHook('page.changeNum:after', $page);
        },
        'page.changeSlug:after' => function ($page, $oldPage) {
            CloudflareCache::handlePageHook('page.changeSlug:after', $page, $oldPage);
        },
        'page.changeStatus:after' => function ($page, $oldPage) {
            CloudflareCache::handlePageHook('page.changeStatus:after', $page);
        },
        'page.changeTemplate:after' => function ($page, $oldPage) {
            CloudflareCache::handlePageHook('page.changeTemplate:after', $page);
        },
        'page.changeTitle:after' => function ($page, $oldPage) {
            CloudflareCache::handlePageHook('page.changeTitle:after', $page);
        },
        'page.create:after' => function ($page) {
            CloudflareCache::handlePageHook('page.create:after', $page);
        },
        'page.delete:after' => function ($status, $oldPage) {
            CloudflareCache::handlePageHook('page.delete:after', $oldPage);
        },
        'page.update:after' => function ($page, $oldPage) {
            CloudflareCache::handlePageHook('page.changeSlug:after', $page, $oldPage);
        },
        'site.update:after' => function ($site, $oldSite) {
            CloudflareCache::handleSiteHook('site.update:after', $site);
        },
    ],
]);

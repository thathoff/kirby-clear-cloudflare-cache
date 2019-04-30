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
        'dependantUrlsForFile'  => function ($hook, $file, $oldFile = null) {
            $clearParentOnly = in_array($hook, [
                'file.changeSort:after',
                'file.create:after',
                'file.update:after',
            ]);
            
            $urls = $clearParentOnly ? [$file->parent()->url()] : [$file->url(), $file->parent()->url()];
            if ($oldFile && !$clearParentOnly) {
                $urls[] = $oldFile->url();
                // Shouldn't need to add $oldFile->parent()->url() because the parent should be the same as the "new" file's parent.
            }
            return $urls;
        },
        'dependantUrlsForSite'  => function ($hook, $site, $oldSite = null) {
            return $site->url();
        },
    ],
    'hooks' => [
        // Page
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
        
        // File
        'file.changeName:after' => function ($file, $oldFile) {
            CloudflareCache::handleFileHook('file.changeName:after', $file, $oldFile);
        },
        'file.changeSort:after' => function ($file, $oldFile) {
            CloudflareCache::handleFileHook('file.changeSort:after', $file, $oldFile);
        },
        'file.create:after' => function ($file) {
            CloudflareCache::handleFileHook('file.create:after', $file);
        },
        'file.delete:after' => function ($status, $oldFile) {
            CloudflareCache::handleFileHook('file.delete:after', $oldFile);
        },
        'file.replace:after' => function ($file, $oldFile) {
            CloudflareCache::handleFileHook('file.replace:after', $file, $oldFile);
        },
        'file.update:after' => function ($file, $oldFile) {
            CloudflareCache::handleFileHook('file.update:after', $file, $oldFile);
        },
        
        // Site
        'site.update:after' => function ($site, $oldSite) {
            CloudflareCache::handleSiteHook('site.update:after', $site);
        },
    ],
]);

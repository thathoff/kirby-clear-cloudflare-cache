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
        'page.changeNum:after' => function ($newPage) {
            CloudflareCache::handlePageHook('page.changeNum:after', $newPage);
        },
        'page.changeSlug:after' => function ($newPage, $oldPage) {
            CloudflareCache::handlePageHook('page.changeSlug:after', $newPage, $oldPage);
        },
        'page.changeStatus:after' => function ($newPage) {
            CloudflareCache::handlePageHook('page.changeStatus:after', $newPage);
        },
        'page.changeTemplate:after' => function ($newPage) {
            CloudflareCache::handlePageHook('page.changeTemplate:after', $newPage);
        },
        'page.changeTitle:after' => function ($newPage) {
            CloudflareCache::handlePageHook('page.changeTitle:after', $newPage);
        },
        'page.create:after' => function ($page) {
            CloudflareCache::handlePageHook('page.create:after', $page);
        },
        'page.delete:after' => function ($status, $page) {
            CloudflareCache::handlePageHook('page.delete:after', $page);
        },
        'page.update:after' => function ($newPage, $oldPage) {
            CloudflareCache::handlePageHook('page.changeSlug:after', $newPage, $oldPage);
        },

        // File
        'file.changeName:after' => function ($newFile, $oldFile) {
            CloudflareCache::handleFileHook('file.changeName:after', $newFile, $oldFile);
        },
        'file.changeSort:after' => function ($newFile, $oldFile) {
            CloudflareCache::handleFileHook('file.changeSort:after', $newFile, $oldFile);
        },
        'file.create:after' => function ($file) {
            CloudflareCache::handleFileHook('file.create:after', $file);
        },
        'file.delete:after' => function ($status, $file) {
            CloudflareCache::handleFileHook('file.delete:after', $file);
        },
        'file.replace:after' => function ($newFile, $oldFile) {
            CloudflareCache::handleFileHook('file.replace:after', $newFile, $oldFile);
        },
        'file.update:after' => function ($newFile, $oldFile) {
            CloudflareCache::handleFileHook('file.update:after', $newFile, $oldFile);
        },

        // Site
        'site.update:after' => function ($newSite, $oldSite) {
            CloudflareCache::handleSiteHook('site.update:after', $newSite);
        },
    ],
]);

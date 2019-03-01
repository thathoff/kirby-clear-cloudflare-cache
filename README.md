# Clear Cloudflare Cache Kirby Plugin

This Kirby plugin can automatically purge Cloudflare cached URLs. This plugin is used on [The Streamable](https://thestreamable.com).

## Installation

### Download

Download and copy this repository to `/site/plugins/kirby-clear-cloudflare-cache`.

### Git submodule

```
git submodule add https://github.com/thestreamable/kirby-clear-cloudflare-cache.git site/plugins/kirby-clear-cloudflare-cache
```

### Composer

```
composer require thestreamable/kirby-clear-cloudflare-cache
```

## Setup

At a minimum, you must set the following options in your `config.php` file:

```php
'thestreamable.clearcloudflarecache.cloudflareZoneID' => 'YOUR_CF_ZONE_ID',
'thestreamable.clearcloudflarecache.cloudflareEmail'  => 'YOUR_CF_EMAIL',
'thestreamable.clearcloudflarecache.cloudflareAPIKey' => 'YOUR_CF_API_KEY',
```

## Options

### `thestreamable.clearcloudflarecache.cloudflareZoneID`
This must be set to your Cloudflare Zone ID (available in the Overview dashboard).

### `thestreamable.clearcloudflarecache.cloudflareEmail`
This must be set to the email address of your Cloudflare account.

### `thestreamable.clearcloudflarecache.cloudflareAPIKey`
This must be set to your Cloudflare API Key (available in the Profile page).

### `thestreamable.clearcloudflarecache.dependantUrlsForPage`
This must be a function that returns what URL(s) should be cleared after a page modification.

By default, simply returns the URL of the page itself and potentially the previous URL (in the case of a slug change).

```php
function ($hook, $page, $oldPage = null) {
    return $oldPage ? [$page->url(), $oldPage->url()] : $page->url();
}
```

If you know that a change to one page affects other pages, you could include them as well. For example, the following would clear the Cloudflare cache for a modified page siblings (including the affected page) and parent pages.

```php
'thestreamable.clearcloudflarecache.dependantUrlsForPage'=> function ($hook, $page, $oldPage = null) {
    return $page->parents()->add($page->siblings(true));
},
```

Or, a more elaborate example could include a sitemap and content representations:

```php
'thestreamable.clearcloudflarecache.dependantUrlsForPage'=> function ($hook, $page, $oldPage = null) {
    $urls = [];
    $urls[] = $page->url();
    $urls[] = $page->url() . '.json';
    if ($oldPage) {
        $urls[] = $oldPage->url();
        $urls[] = $oldPage->url() . '.json';
    }
    $urls[] = page('sitemap')->url();
    $urls[] = page('sitemap')->url() . '.xml';
    return $urls;
},
```

The function may return:
- a single Page object
- a single URL string
- a Pages collection of Page objects
- an array of URL strings and/or Page objects
- null, empty array, or empty Pages collection (this will cause no cache to be cleared)

Duplicate URLs will automatically be filtered out.

All URL strings must be absolute URLs (`https://www.example.com/blog`), not relative (`/blog`);

## License

MIT

## Credits

- [Neil Daniels](https://github.com/neildaniels) of [The Streamable](https://thestreamable.com)

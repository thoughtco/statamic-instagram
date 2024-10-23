# Statamic Instagram

> Statamic Instagram lets you pull down users media and profiles from within your Statamic site.

## How to Install

Run the following command from your project root:

``` bash
composer require thoughtco/statamic-instagram
```

## Configuration

A configuration file can be published by running the following command:

`php artisan vendor:publish --tag=statamic-instagram-config`

This will create `statamic-instagram.php` in your config folder.


## Usage

This package provides a light wrapper around https://github.com/pgrimaud/instagram-user-feed, which uses web scraping to get Instagram posts. This means you don't need to set up a Facebook or Instagram App to access it.

It requires an Instagram username and password to pull down data - you should create a throwaway Instagram account for this rather than using an actual account. Add the username and password to your .env:

`STATAMIC_INSTAGRAM_USERNAME` and `STATAMIC_INSTAGRAM_PASSWORD`.

### Tag

This package provides an `{{ instagram }}` tag:

```antlers
{{ instagram profile="robertdowneyjr" limit="6" as="ig" }}
    <div>
        {{ ig }}
            {{ media }}
            <a href="{{ link }}" >
                <img src="{{ thumbnailSrc }}" alt="{{ caption }}" />
            </a>
            {{ /media }}
        {{ /ig }}
    </div>
{{ /instagram }}


```

### API

This package exposes the API provided by the instagram-user-feed package, so you can call any of the methods on it, eg:

```php
app(\Instagram\Api::class)->getProfile('robertdowneyjr');
```

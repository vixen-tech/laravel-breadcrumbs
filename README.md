# Laravel Breadcrumbs

[![Latest Version on Packagist](https://img.shields.io/packagist/v/vixen/laravel-breadcrumbs.svg?style=flat-square)](https://packagist.org/packages/vixen/laravel-breadcrumbs)
[![Total Downloads](https://img.shields.io/packagist/dt/vixen/laravel-breadcrumbs.svg?style=flat-square)](https://packagist.org/packages/vixen/laravel-breadcrumbs)

Laravel package that allows simple creation of breadcrumbs. Works perfectly with Blade and Inertia.js.

```php
public function show(Post $post) {
    crumbs('Posts', '/posts')->add("Show Post #{$post->id}", route('posts.show', $post));
}
```

```html
<section>
    @crumbs
</section>

<main>Main Content</main>
```

```tsx
export function HomePage({ breadcrumbs }: { breadcrumbs: Breadcrumbs }) {
    return (<div><Breadcrumbs breadcrumbs={breadcrumbs} /></div>)
}
```

## Installation

You can install the package by running this command in your console:

```shell
composer require vixen/laravel-breadcrumbs
```

The Service Provider will be automatically discovered.

### Vendor Files

You can publish both the configuration and view files with these commands:

```shell
php artisan vendor:publish --tag="breadcrumbs-config"
```
```shell
php artisan vendor:publish --tag="breadcrumbs-views"
```

## Usage

There are two different places to populate a breadcrumbs list:
1. In your routes file, e.g. `web.php`.
2. Directly in your route's action, e.g. closure or controller.

### Routes File

```php
use Vixen\Breadcrumbs\Breadcrumbs;

Route::get('posts', [PostController::class, 'index'])->crumbs(function (Breadcrumbs $crumbs) {
    $crumbs->add('Posts', '/posts'); // Here we are using a hard-coded URL
});
```

With this method you can get breadcrumbs declaration out of the way, however we don't have access to route model bindings. That's a case where we can put breadcrumbs declaration in our controller.

> This route macro has the same signature as the `crumbs` helper function.

### Controller

```php
public function show(Post $post) {
    crumbs('Posts', '/posts')->add("Show Post #{$post->id}", route('posts.show', $post));
}
```

This way you can use route model bindings to build your breadcrumbs, such as showing a resource's ID.

### Notations

There are are also three different ways of building a breadcrumbs list:

#### Breadcrumbs Class

```php
use Vixen\Breadcrumbs\Breadcrumbs;

public function show(Post $post, Breadcrumbs $crumbs) {
    $crumbs->add("Show Post #{$post->id}", route('posts.show', $post));
}
```

or

```php
use Vixen\Breadcrumbs\Breadcrumbs;

public function show(Post $post) {
    Breadcrumbs::instance()->add("Show Post #{$post->id}", route('posts.show', $post));
}
```

#### Crumbs Façade

```php
use Vixen\Breadcrumbs\Facades\Crumbs;

public function show(Post $post) {
    Crumbs::add("Show Post #{$post->id}", route('posts.show', $post));
}
```

#### Helper Function

This is the one we have used so far

```php
public function show(Post $post) {
    crumbs()->add("Show Post #{$post->id}", route('posts.show', $post));
}
```

If no parameters are passed, the function will return an instance of the main `Breadcrumbs` class.

### Rendering

You can render the breadcrumbs list by calling `crumbs()->render()` inside a Blade view or using a custom directive:

```html
<section>
    {{ crumbs()->render() }}
</section>

<!-- or -->

<section>
    @crumbs
</section>
```

Both notations accept an optional parameter `$view`:
- `crumbs()->render('breadcrumbs::custom-view')`
- `@crumbs(breadcrumbs::custom-view)`

You can either customize already existing views that come with the package by running:

```shell
php artisan vendor:publish --tag="breadcrumbs-views"
```

Or specify a custom view inside `config('breadcrumbs.view')`.

#### View Customization

Let's say we want to create a completely new view for our breadcrumbs. We start by creating a new Blade file inside `resources/views/vendor/breadcrumbs/custom-theme.blade.php` (I prefer to put even custom views inside the `vendor` folder, but feel free to put them anywhere you like as long as it's inside `resources/views` folder).

Let's take a look the default view file (`resources/views/vendor/breadcrumbs/plain.blade.php`):

```html
<nav aria-label="Breadcrumb">
    <ol role="list" style="display: flex; align-items: center; gap: 1rem">
        @foreach ($breadcrumbs as $breadcrumb)
            @if (!$loop->first)
                <li>/</li>
            @endif
    
            @if ($breadcrumb->active)
                <li>{{ $breadcrumb->title }}</li>
            @else
                <a href="{{ $breadcrumb->path }}">
                    {{ $breadcrumb->title }}
                </a>
            @endif
        @endforeach
    </ol>
</nav>
```

A few things to note here:
- `$breadcrumbs` is automatically passed to the view. This is the instance of `Breadcrumbs` class. You can also call `$breadcrumbs->all()` which is the same thing.
- `$breadcrumb->active` is a computed property that simply returns `true` in case the breadcrumb's path is the same as current URL.

## API

### `Breadcrumbs::add(string|array $title, ?string $path = null)`

A breadcrumb requires a title.

`$path` is a plain string URL. If not provided, the breadcrumb will have no URL.

`$title` accepts both a string and an array. If it's an array, it must contain these keys:
```php
[
    'title' => '',
    'path' => '', // optional
]
```

### `crumbs(string|array|callable|null $title = null, ?string $path = null)`

If you call this helper function without any parameter, it will simply return an instance of `Breadcrumbs` as mentioned above. Otherwise it accepts the same parameters as `Breadcrumbs::add()`.

Exclusively to this function, the `$title` parameter also accepts a closure (the same goes for the `Route::crumbs()` macro as shown in the example above):

```php
use Vixen\Breadcrumbs\Breadcrumbs;

public function show(Post $post) {
    crumbs(function (Breadcrumbs $crumbs) use ($post) {
        $crumbs->add('All Posts', route('posts.index'));
        $crumbs->add("Show Post #{$post->id}", route('posts.show', $post));
    });
}
```

### Breadcrumb Item

A single breadcrumb item has `title`, `path` and `active` properties. 

## Changelog

The [CHANGELOG](CHANGELOG.md) file will tell you about all changes to this package.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Credits

- [Alex Torscho](https://github.com/atorscho)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.

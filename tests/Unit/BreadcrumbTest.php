<?php

use Vixen\Breadcrumbs\Breadcrumbs;
use Vixen\Breadcrumbs\Exceptions\InvalidBreadcrumbOptions;
use Vixen\Breadcrumbs\Facades\Crumbs;
use Illuminate\Routing\RouteCollection;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;

function mockRoutes(): void
{
    $collection = new RouteCollection();
    $collection->add(Route::get('/posts', fn() => 'all posts')->name('posts.index'));
    $collection->add(Route::get('/posts/{post}', fn($post) => 'post #'.$post)->name('posts.show'));

    Route::shouldReceive('has')->andReturn(true);
    Route::shouldReceive('getRoutes')->andReturn($collection);
}

describe('Adding breadcrumbs', function () {
    it('accepts a title only and path is inferred', function () {
        URL::shouldReceive('current')->andReturn('/main-section');

        Crumbs::add('Main Section');

        expect(crumbs()[0]->path)->toBe('/main-section');
    });

    it('accepts a title and a path', function () {
        Breadcrumbs::instance()
            ->add('Main Section', '/main')
            ->add('Last Section', '/main/last');

        expect(crumbs()[0]->title)->toBe('Main Section')
            ->and(crumbs()[0]->path)->toBe('http://localhost/main')
            ->and(crumbs()[1]->title)->toBe('Last Section');
    });

    it('accepts a route name', function () {
        mockRoutes();

        Crumbs::add('All Posts', 'posts.index');

        expect(crumbs()[0]->path)->toBe('http://localhost/posts');
    });

    it('accepts route parameters', function () {
        mockRoutes();

        Crumbs::add('Show Post #1', 'posts.show', [1]);
        Crumbs::add('Show Post #2', 'posts.show', ['post' => 2]);

        expect(crumbs()[0]->path)->toBe('http://localhost/posts/1')
            ->and(crumbs()[1]->path)->toBe('http://localhost/posts/2');
    });

    it('accepts a closure', function () {
        crumbs(function (Breadcrumbs $crumbs) {
            $crumbs
                ->add('Main Page', '/main')
                ->add('Sub Page', '/main/sub')
                ->add('Current Page', '/main/sub/current');
        });

        expect(crumbs()[0]->title)->toBe('Main Page')
            ->and(crumbs()[2]->title)->toBe('Current Page');
    });
});

describe('Options array', function () {
    it('validates options array', function () {
        crumbs([
            'name' => 'Invalid key',
        ]);
    })->throws(InvalidBreadcrumbOptions::class);

    it('accepts an array of options', function () {
        crumbs([
            'title' => 'About Page',
            'path' => '/about',
        ]);

        expect(crumbs()[0]->title)->toBe('About Page')
            ->and(crumbs()[0]->path)->toBe('http://localhost/about');
    });

    it('accepts an array of options with a route name', function () {
        mockRoutes();

        crumbs([
            'title' => 'Post',
            'path' => 'posts.show',
            'params' => 10,
        ]);

        expect(crumbs()[0]->path)->toBe('http://localhost/posts/10');
    });
});

describe('Active page detection', function () {
    it('determines a current page', function () {
        Crumbs::add('Home Page', '/home');

        URL::shouldReceive('current')->andReturn('http://localhost/about');
        URL::shouldReceive('to')->andReturn('http://localhost/about');
        Crumbs::add('About Us', '/about');

        expect(crumbs()[0]->active)->toBeFalse()
            ->and(crumbs()[1]->active)->toBeTrue();
    });

    it('determines a current page using route names', function () {
        Crumbs::add('Posts', 'posts.index');

        URL::shouldReceive('current')->andReturn('http://localhost/posts/1');
        URL::shouldReceive('route')->andReturn('http://localhost/posts/1');
        mockRoutes();
        Crumbs::add('Show Post', 'posts.show', 1);

        expect(crumbs()[0]->active)->toBeFalse()
            ->and(crumbs()[1]->active)->toBeTrue();
    });
});

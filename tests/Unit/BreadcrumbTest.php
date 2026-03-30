<?php

use Vixen\Breadcrumbs\Breadcrumbs;
use Vixen\Breadcrumbs\Exceptions\InvalidBreadcrumbOptions;
use Vixen\Breadcrumbs\Facades\Crumbs;
use Illuminate\Support\Facades\URL;

describe('Adding breadcrumbs', function () {
    it('accepts a title only with null path and active', function () {
        Crumbs::add('Main Section');

        expect(crumbs()[0]->title)->toBe('Main Section')
            ->and(crumbs()[0]->path)->toBeNull()
            ->and(crumbs()[0]->active)->toBeFalse();
    });

    it('accepts a title and a path', function () {
        Breadcrumbs::instance()
            ->add('Main Section', '/main')
            ->add('Last Section', '/main/last');

        expect(crumbs()[0]->title)->toBe('Main Section')
            ->and(crumbs()[0]->path)->toBe('/main')
            ->and(crumbs()[1]->title)->toBe('Last Section')
            ->and(crumbs()[1]->path)->toBe('/main/last');
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
    it('accepts an array of options without a path', function () {
        crumbs([
            'title' => 'Current Page',
        ]);

        expect(crumbs()[0]->title)->toBe('Current Page')
            ->and(crumbs()[0]->path)->toBeNull()
            ->and(crumbs()[0]->active)->toBeFalse();
    });

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
            ->and(crumbs()[0]->path)->toBe('/about');
    });
});

describe('Active page detection', function () {
    it('determines a current page', function () {
        URL::shouldReceive('current')->andReturn('/about');

        Crumbs::add('Home Page', '/home');
        Crumbs::add('About Us', '/about');

        expect(crumbs()[0]->active)->toBeFalse()
            ->and(crumbs()[1]->active)->toBeTrue();
    });
});

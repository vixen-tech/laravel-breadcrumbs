<?php

namespace Vixen\Breadcrumbs\Tests;

use Vixen\Breadcrumbs\Breadcrumbs;
use Vixen\Breadcrumbs\Exceptions\InvalidBreadcrumbOptions;
use Vixen\Breadcrumbs\Facades\Crumbs;
use Illuminate\Routing\RouteCollection;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use PHPUnit\Framework\Attributes\Test;

class BreadcrumbTest extends TestCase
{
    #[Test]
    public function it_accepts_a_title_only_and_path_is_inferred()
    {
        URL::shouldReceive('current')->andReturn('/main-section');

        Crumbs::add('Main Section');

        $this->assertEquals('/main-section', crumbs()[0]->path);
    }

    #[Test]
    public function it_accepts_a_title_and_a_path()
    {
        Breadcrumbs::instance()
            ->add('Main Section', '/main')
            ->add('Last Section', '/main/last');

        $this->assertEquals('Main Section', crumbs()[0]->title);
        $this->assertEquals('http://localhost/main', crumbs()[0]->path);
        $this->assertEquals('Last Section', crumbs()[1]->title);
    }

    #[Test]
    public function it_accepts_a_route_name()
    {
        $this->mockRoutes();

        Crumbs::add('All Posts', 'posts.index');

        $this->assertEquals('http://localhost/posts', crumbs()[0]->path);
    }

    #[Test]
    public function it_accepts_route_parameters()
    {
        $this->mockRoutes();

        Crumbs::add('Show Post #1', 'posts.show', [1]);
        Crumbs::add('Show Post #2', 'posts.show', ['post' => 2]);

        $this->assertEquals('http://localhost/posts/1', crumbs()[0]->path);
        $this->assertEquals('http://localhost/posts/2', crumbs()[1]->path);
    }

    #[Test]
    public function it_validates_options_array()
    {
        $this->expectException(InvalidBreadcrumbOptions::class);

        crumbs([
            'name' => 'Invalid key',
        ]);
    }

    #[Test]
    public function it_accepts_an_array_of_options()
    {
        crumbs([
            'title' => 'About Page',
            'path' => '/about',
        ]);

        $this->assertEquals('About Page', crumbs()[0]->title);
        $this->assertEquals('http://localhost/about', crumbs()[0]->path);
    }

    #[Test]
    public function it_accepts_an_array_of_options_with_a_route_name()
    {
        $this->mockRoutes();

        crumbs([
            'title' => 'Post',
            'path' => 'posts.show',
            'params' => 10,
        ]);

        $this->assertEquals('http://localhost/posts/10', crumbs()[0]->path);
    }

    #[Test]
    public function it_accepts_a_closure()
    {
        crumbs(function (Breadcrumbs $crumbs) {
            $crumbs
                ->add('Main Page', '/main')
                ->add('Sub Page', '/main/sub')
                ->add('Current Page', '/main/sub/current');
        });

        $this->assertEquals('Main Page', crumbs()[0]->title);
        $this->assertEquals('Current Page', crumbs()[2]->title);
    }

    #[Test]
    public function it_determines_a_current_page()
    {
        Crumbs::add('Home Page', '/home');

        URL::shouldReceive('current')->andReturn('http://localhost/about');
        URL::shouldReceive('to')->andReturn('http://localhost/about');
        Crumbs::add('About Us', '/about');

        $this->assertFalse(crumbs()[0]->active);
        $this->assertTrue(crumbs()[1]->active);
    }

    #[Test]
    public function it_determines_a_current_page_using_route_names()
    {
        Crumbs::add('Posts', 'posts.index');

        URL::shouldReceive('current')->andReturn('http://localhost/posts/1');
        URL::shouldReceive('route')->andReturn('http://localhost/posts/1');
        $this->mockRoutes();
        Crumbs::add('Show Post', 'posts.show', 1);

        $this->assertFalse(crumbs()[0]->active);
        $this->assertTrue(crumbs()[1]->active);
    }

    private function mockRoutes(): void
    {
        $collection = new RouteCollection();
        $collection->add(Route::get('/posts', fn() => 'all posts')->name('posts.index'));
        $collection->add(Route::get('/posts/{post}', fn($post) => 'post #'.$post)->name('posts.show'));

        Route::shouldReceive('has')->andReturn(true);
        Route::shouldReceive('getRoutes')->andReturn($collection);
    }
}

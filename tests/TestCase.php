<?php

namespace Vixen\Breadcrumbs\Tests;

use Vixen\Breadcrumbs\BreadcrumbsServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{
    protected function getPackageProviders($app)
    {
        return [
            BreadcrumbsServiceProvider::class,
        ];
    }
}

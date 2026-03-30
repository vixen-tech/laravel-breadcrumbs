<?php

use Vixen\Breadcrumbs\Breadcrumbs;
use Vixen\Breadcrumbs\Exceptions\InvalidBreadcrumbOptions;

if (!function_exists('crumbs')) {
    /**
     * A shorthand for calling Crumbs façade.
     *
     * @throws InvalidBreadcrumbOptions
     */
    function crumbs(string|array|callable|null $title = null, ?string $path = null, array $extra = []): Breadcrumbs
    {
        return tap(app(Breadcrumbs::class), function (Breadcrumbs $crumbs) use ($path, $title, $extra) {
            if ($title instanceof Closure) {
                $title($crumbs);
            } elseif (!is_null($title)) {
                $crumbs->add($title, $path, $extra);
            }
        });
    }
}

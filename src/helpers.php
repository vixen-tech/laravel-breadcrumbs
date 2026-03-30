<?php

use Vixen\Breadcrumbs\Breadcrumbs;

if (!function_exists('crumbs')) {
    /**
     * A shorthand for calling Crumbs façade.
     */
    function crumbs(string|array|callable|null $title = null, ?string $path = null, mixed $params = null): Breadcrumbs
    {
        return tap(app(Breadcrumbs::class), function (Breadcrumbs $crumbs) use ($params, $path, $title) {
            if (is_callable($title)) {
                $title($crumbs);
            } elseif (!is_null($title)) {
                $crumbs->add($title, $path, $params);
            }
        });
    }
}

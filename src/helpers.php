<?php

use Vixen\Breadcrumbs\Breadcrumbs;

if (!function_exists('crumbs')) {
    /**
     * A shorthand for calling Crumbs façade.
     */
    function crumbs(string|array|\Closure|null $title = null, ?string $path = null): Breadcrumbs
    {
        return tap(app(Breadcrumbs::class), function (Breadcrumbs $crumbs) use ($path, $title) {
            if ($title instanceof \Closure) {
                $title($crumbs);
            } elseif (!is_null($title)) {
                $crumbs->add($title, $path);
            }
        });
    }
}

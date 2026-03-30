<?php

namespace Vixen\Breadcrumbs;

use Vixen\Breadcrumbs\Exceptions\InvalidBreadcrumbOptions;
use Illuminate\Support\Facades\URL;

class Breadcrumb
{
    /**
     * Breadcrumb item's label.
     */
    public readonly string $title;

    /**
     * A URL to the breadcrumb's item.
     */
    public readonly ?string $path;

    /**
     * Whether the breadcrumb is a current page.
     */
    public readonly bool $active;

    public function __construct(
        string|array $title,
        ?string $path = null,
    )
    {
        if (is_string($title)) {
            $this->title = $title;
            $this->path = $path;
        } else {
            $this->parseOptions($title);
        }

        $this->active = $this->path !== null && $this->path === URL::current();
    }

    /**
     * Convert breadcrumb options into a title and a URL.
     *
     * @throws InvalidBreadcrumbOptions
     */
    protected function parseOptions(array $options): void
    {
        if (!isset($options['title'])) {
            throw new InvalidBreadcrumbOptions('Parameter "$title" must either be a string or an array containing a title key.');
        }

        $this->title = $options['title'];
        $this->path = $options['path'] ?? null;
    }
}

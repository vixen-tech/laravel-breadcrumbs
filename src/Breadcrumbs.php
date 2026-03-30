<?php

namespace Vixen\Breadcrumbs;

use ArrayAccess;
use Countable;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use IteratorAggregate;
use Traversable;
use Vixen\Breadcrumbs\Exceptions\InvalidBreadcrumbOptions;

class Breadcrumbs implements Arrayable, ArrayAccess, Countable, IteratorAggregate, Jsonable
{
    /**
     * A list of breadcrumb items.
     *
     * @var Collection<int, Breadcrumb | Breadcrumb[]>
     */
    protected Collection $crumbs;

    public function __construct()
    {
        $this->crumbs = collect();
    }

    public static function instance(): static
    {
        return app(static::class);
    }

    /**
     * @return Collection<int, Breadcrumb>
     */
    public function all(): Collection
    {
        return $this->crumbs;
    }

    /**
     * @throws InvalidBreadcrumbOptions
     */
    public function add(string|array $title, ?string $path = null, array $extra = []): static
    {
        if (is_array($title) && array_is_list($title)) {
            $items = [];

            foreach ($title as $item) {
                $items[] = new Breadcrumb($item);
            }

            $this->crumbs[] = $items;
        } else {
            $this->crumbs[] = new Breadcrumb($title, $path, $extra);
        }

        return $this;
    }

    public function render(?string $view = null): View
    {
        return view($view ?: config('breadcrumbs.view'))->with('breadcrumbs', $this->all());
    }

    public function __toString(): string
    {
        return $this->render();
    }

    public function toArray(): array
    {
        return $this->crumbs->toArray();
    }

    public function offsetExists(mixed $offset): bool
    {
        return isset($this->crumbs[$offset]);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->crumbs[$offset];
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->crumbs[$offset] = $value;
    }

    public function offsetUnset(mixed $offset): void
    {
        $this->crumbs->forget($offset);
    }

    public function count(): int
    {
        return count($this->crumbs);
    }

    public function getIterator(): Traversable
    {
        return $this->crumbs;
    }

    public function toJson($options = 0)
    {
        return $this->crumbs->toJson($options);
    }
}

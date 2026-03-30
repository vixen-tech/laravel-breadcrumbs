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

class Breadcrumbs implements Arrayable, ArrayAccess, Countable, IteratorAggregate, Jsonable
{
    /**
     * A list of breadcrumbs' items.
     *
     * @var Collection<int, Breadcrumb>
     */
    protected Collection $crumbs;

    public function __construct()
    {
        $this->crumbs = collect();
    }

    /**
     * Get current instance of the class.
     */
    public static function instance(): static
    {
        return app(static::class);
    }

    /**
     * Return all existing breadcrumbs.
     *
     * @return Collection<int, Breadcrumb>
     */
    public function all(): Collection
    {
        return $this->crumbs;
    }

    /**
     * Add a new breadcrumb item.
     */
    public function add(string|array $title, ?string $path = null): static
    {
        $this->crumbs[] = new Breadcrumb($title, $path);

        return $this;
    }

    /**
     * Render a breadcrumbs list.
     */
    public function render(?string $view = null): View
    {
        return view($view ?: config('breadcrumbs.view'))->with('breadcrumbs', $this->all());
    }

    /**
     * Stringify the class instance.
     */
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

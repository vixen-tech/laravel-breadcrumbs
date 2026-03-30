<?php

namespace Vixen\Breadcrumbs\Tests;

use Vixen\Breadcrumbs\Breadcrumb;
use Vixen\Breadcrumbs\Breadcrumbs;
use Vixen\Breadcrumbs\Facades\Crumbs;
use PHPUnit\Framework\Attributes\Test;

class BreadcrumbsTest extends TestCase
{
    #[Test]
    public function it_returns_a_class_singleton()
    {
        $this->assertInstanceOf(Breadcrumbs::class, crumbs());
        $this->assertInstanceOf(Breadcrumbs::class, Breadcrumbs::instance());
        $this->assertSame(crumbs(), Breadcrumbs::instance());
    }

    #[Test]
    public function it_can_be_converted_into_json_format()
    {
        crumbs('First', '#first')->add('Second', '#second');

        $this->assertIsString(Crumbs::toJson());
        $this->assertEquals('[{"title":"First","path":"#first","active":false},{"title":"Second","path":"#second","active":false}]', Crumbs::toJson());
    }

    #[Test]
    public function it_returns_a_breadcrumb_by_key()
    {
        crumbs('First', '#first')
            ->add('Second', '#second')
            ->add('Third', '#third');

        $this->assertEquals('First', crumbs()[0]->title);
        $this->assertEquals('Third', crumbs()[2]->title);
    }

    #[Test]
    public function it_checks_whether_a_breadcrumb_exists_at_a_specified_index()
    {
        crumbs('First', '#first')
            ->add('Second', '#second')
            ->add('Third', '#third');

        $this->assertArrayHasKey(1, crumbs());
        $this->assertArrayNotHasKey(5, crumbs());
    }

    #[Test]
    public function it_sets_a_breadcrumb_at_a_specified_index()
    {
        crumbs('First', '#first');
        crumbs()[3] = new Breadcrumb('Second', '#second');

        $this->assertCount(2, crumbs()->all());
        $this->assertEquals('First', crumbs()[0]->title);
        $this->assertEquals('Second', crumbs()[3]->title);
    }

    #[Test]
    public function it_deletes_a_breadcrumb_at_a_given_index()
    {
        crumbs('First', '#first')
            ->add('Second', '#second')
            ->add('Third', '#third');

        unset(crumbs()[2]);

        $this->assertCount(2, crumbs()->all());
        $this->assertArrayNotHasKey(2, crumbs());
    }

    #[Test]
    public function it_counts_a_total_number_of_breadcrumb_items()
    {
        crumbs('1', '#first')
            ->add('2', '#second')
            ->add('3', '#third')
            ->add('4', '#fourth');

        $this->assertCount(4, crumbs());
    }

    #[Test]
    public function it_is_iterable()
    {
        crumbs('1', '#first')
            ->add('2', '#second')
            ->add('3', '#third');

        $i = 0;

        foreach (crumbs() as $k) {
            $i++;
        }

        $this->assertEquals(3, $i);
    }

    #[Test]
    public function it_is_arrayable()
    {
        crumbs('1', '#first')->add('2', '#second')->add('3', '#third');

        $this->assertIsArray(Crumbs::toArray());
        $this->assertCount(3, crumbs()->toArray());
    }
}

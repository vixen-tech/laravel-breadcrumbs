<?php

use Vixen\Breadcrumbs\Breadcrumb;
use Vixen\Breadcrumbs\Breadcrumbs;
use Vixen\Breadcrumbs\Facades\Crumbs;

it('returns a class singleton', function () {
    expect(crumbs())->toBeInstanceOf(Breadcrumbs::class)
        ->and(Breadcrumbs::instance())->toBeInstanceOf(Breadcrumbs::class)
        ->and(crumbs())->toBe(Breadcrumbs::instance());
});

describe('Data formats', function () {
    it('can be converted into json format', function () {
        crumbs('First', '#first')->add('Second', '#second');

        expect(Crumbs::toJson())->toBeString()
            ->and(Crumbs::toJson())->toBe('[{"title":"First","path":"#first","active":false,"extra":[]},{"title":"Second","path":"#second","active":false,"extra":[]}]');
    });

    it('is arrayable', function () {
        crumbs('1', '#first')->add('2', '#second')->add('3', '#third');

        expect(Crumbs::toArray())->toBeArray()
            ->and(crumbs()->toArray())->toHaveCount(3);
    });
});

describe('Collection behavior', function () {
    it('returns a breadcrumb by key', function () {
        crumbs('First', '#first')
            ->add('Second', '#second')
            ->add('Third', '#third');

        expect(crumbs()[0]->title)->toBe('First')
            ->and(crumbs()[2]->title)->toBe('Third');
    });

    it('checks whether a breadcrumb exists at a specified index', function () {
        crumbs('First', '#first')
            ->add('Second', '#second')
            ->add('Third', '#third');

        expect(crumbs())->toHaveKey(1)
            ->and(crumbs())->not->toHaveKey(5);
    });

    it('sets a breadcrumb at a specified index', function () {
        crumbs('First', '#first');
        crumbs()[3] = new Breadcrumb('Second', '#second');

        expect(crumbs()->all())->toHaveCount(2)
            ->and(crumbs()[0]->title)->toBe('First')
            ->and(crumbs()[3]->title)->toBe('Second');
    });

    it('deletes a breadcrumb at a given index', function () {
        crumbs('First', '#first')
            ->add('Second', '#second')
            ->add('Third', '#third');

        unset(crumbs()[2]);

        expect(crumbs()->all())->toHaveCount(2)
            ->and(crumbs())->not->toHaveKey(2);
    });

    it('counts a total number of breadcrumb items', function () {
        crumbs('1', '#first')
            ->add('2', '#second')
            ->add('3', '#third')
            ->add('4', '#fourth');

        expect(crumbs())->toHaveCount(4);
    });

    it('is iterable', function () {
        crumbs('1', '#first')
            ->add('2', '#second')
            ->add('3', '#third');

        $i = 0;

        foreach (crumbs() as $k) {
            $i++;
        }

        expect($i)->toBe(3);
    });

    it('accepts title, path and extra data as an array', function () {
        crumbs([
            'title' => 'First',
            'path' => '/first',
        ]);

        expect(crumbs()[0]->title)->toBe('First');
    });

    it('accepts an array of items at a single position', function () {
        crumbs([
            ['title' => 'Section 1.1', 'path' => '/section-1-1'],
            ['title' => 'Section 1.2', 'path' => '/section-1-2'],
        ])
            ->add('Section 2', '#section-2');

        expect(crumbs()[0][0]->title)->toBe('Section 1.1')
            ->and(crumbs()[0][1]->title)->toBe('Section 1.2')
            ->and(crumbs()[1]->title)->toBe('Section 2');
    });
});

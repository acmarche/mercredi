<?php

namespace AcMarche\Mercredi\Tests\Pest;

test('example', function () {
    expect(true)->toBeTrue();
});

it('as a homepage')
    ->get('/')
    ->assertSee('Bienvenue');

it('asserts true is true', function () {
    $this->assertTrue(true);
    expect(true)->toBeTrue();
});
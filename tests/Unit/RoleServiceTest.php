<?php

use App\Models\Role;
use App\Models\User;
use App\Models\Role\Contracts\RoleServiceInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(Tests\TestCase::class, RefreshDatabase::class);

it('creates a role via service', function () {
    /** @var RoleServiceInterface $service */
    $service = app(RoleServiceInterface::class);

    $role = $service->create([
        'name' => 'qa',
        'display_name' => 'QA',
        'description' => 'Quality Assurance',
    ]);

    expect($role)->toBeInstanceOf(Role::class)
        ->and($role->name)->toBe('qa')
        ->and($role->is_active)->toBeTrue();
});

it('lists roles with pagination and filters', function () {
    Role::factory()->count(3)->create();

    /** @var RoleServiceInterface $service */
    $service = app(RoleServiceInterface::class);

    $page = $service->list(['search' => ''], 2);

    expect($page->perPage())->toBe(2)
        ->and($page->total())->toBe(3);
});

it('finds and updates a role', function () {
    $role = Role::factory()->create(['display_name' => 'Old']);

    /** @var RoleServiceInterface $service */
    $service = app(RoleServiceInterface::class);

    $found = $service->find($role->id);
    expect($found?->id)->toBe($role->id);

    $updated = $service->update($role->id, ['display_name' => 'New']);
    expect($updated->display_name)->toBe('New');
});

it('deletes a role with no users', function () {
    $role = Role::factory()->create();

    /** @var RoleServiceInterface $service */
    $service = app(RoleServiceInterface::class);

    $deleted = $service->delete($role->id);
    expect($deleted)->toBeTrue()
        ->and(Role::find($role->id))->toBeNull();
});

it('does not delete a role that has users', function () {
    $role = Role::factory()->create();
    User::factory()->create(['role_id' => $role->id]);

    /** @var RoleServiceInterface $service */
    $service = app(RoleServiceInterface::class);

    $deleted = $service->delete($role->id);
    expect($deleted)->toBeFalse()
        ->and(Role::find($role->id))->not->toBeNull();
});



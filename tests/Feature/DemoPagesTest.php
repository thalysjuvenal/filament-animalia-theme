<?php

use Livewire\Livewire;
use Workbench\App\Filament\Widgets\DemoStats;
use Workbench\App\Models\User;
use Workbench\Database\Seeders\DatabaseSeeder;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Laravel\seed;

beforeEach(function () {
    actingAs(User::query()->create(['name' => 'T', 'email' => 't@example.com', 'password' => bcrypt('x')]));
});

it('renders the kitchen sink', function () {
    get('/admin/kitchen-sink')->assertOk()
        ->assertSee('Perigo secundário')
        ->assertSee('Valor inválido')
        ->assertSee('Cancelado');
});

it('renders the users table', function () {
    seed(DatabaseSeeder::class);

    get('/admin/users')->assertOk()->assertSee('user01@example.com');
});

it('shows the demo stats on the dashboard', function () {
    get('/admin')->assertOk()->assertSee('Pedidos')->assertSee('R$ 48.900', false);

    Livewire::test(DemoStats::class)->assertSee('Pedidos')->assertSee('Receita')->assertSee('Clientes');
});

it('groups navigation under Cadastros and Demonstração', function () {
    get('/admin')->assertOk()->assertSee('Cadastros')->assertSee('Demonstração');
});

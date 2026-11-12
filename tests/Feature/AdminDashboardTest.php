<?php

use App\Models\Category;
use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('bootstraps the first administrator and closes public setup', function () {
    $this->post('/register', [
        'name' => 'Workspace Owner',
        'email' => 'owner@example.test',
        'password' => 'a-strong-password-123',
        'password_confirmation' => 'a-strong-password-123',
    ])->assertRedirect(route('dashboard'));

    $owner = User::query()->firstOrFail();
    expect($owner->hasRole('super-admin'))->toBeTrue()
        ->and(Role::query()->count())->toBe(5);

    $this->get('/register')->assertRedirect(route('dashboard'));
    $this->get(route('dashboard'))
        ->assertOk()
        ->assertSee(__('admin.dashboard.welcome', ['name' => 'Workspace Owner']))
        ->assertSee(__('admin.dashboard.chart_empty'));
});

it('records real sales and decrements product inventory', function () {
    $this->post('/register', [
        'name' => 'Workspace Owner',
        'email' => 'owner@example.test',
        'password' => 'a-strong-password-123',
        'password_confirmation' => 'a-strong-password-123',
    ])->assertRedirect(route('dashboard'));

    $this->post(route('categories.store'), [
        'name' => 'Studio',
        'description' => 'Studio equipment',
    ])->assertRedirect(route('categories.index'));

    $category = Category::query()->firstOrFail();
    $this->post(route('products.store'), [
        'name' => 'Desk lamp',
        'sku' => 'LAMP-001',
        'category_id' => $category->id,
        'price' => '19.99',
        'stock_quantity' => 5,
        'status' => 'active',
    ])->assertRedirect();

    $product = Product::query()->firstOrFail();
    $this->post(route('sales.store'), ['product_id' => $product->id, 'quantity' => 2])
        ->assertRedirect(route('dashboard'));

    $this->assertDatabaseHas('sales', ['product_id' => $product->id, 'quantity' => 2, 'total' => 39.98]);
    expect($product->fresh()->stock_quantity)->toBe(3);

    $this->post(route('sales.store'), ['product_id' => $product->id, 'quantity' => 4])
        ->assertStatus(422);
    expect($product->fresh()->stock_quantity)->toBe(3);

    $this->get(route('dashboard'))->assertSee('39.98');
});

it('uses a product sale price when recording revenue', function () {
    $this->post('/register', [
        'name' => 'Workspace Owner',
        'email' => 'owner@example.test',
        'password' => 'a-strong-password-123',
        'password_confirmation' => 'a-strong-password-123',
    ])->assertRedirect(route('dashboard'));

    $this->post(route('categories.store'), ['name' => 'Sale items']);
    $category = Category::query()->firstOrFail();
    $this->post(route('products.store'), [
        'name' => 'Discounted lamp',
        'sku' => 'SALE-LAMP-001',
        'category_id' => $category->id,
        'price' => '19.99',
        'sale_price' => '15.00',
        'stock_quantity' => 3,
        'status' => 'active',
    ])->assertRedirect();

    $product = Product::query()->where('sku', 'SALE-LAMP-001')->firstOrFail();

    $this->post(route('sales.store'), ['product_id' => $product->id, 'quantity' => 2])
        ->assertRedirect(route('dashboard'));

    $this->assertDatabaseHas('sales', [
        'product_id' => $product->id,
        'quantity' => 2,
        'unit_price' => 15,
        'total' => 30,
    ]);
});

it('prevents assigning the super-admin role and protects that account', function () {
    $this->post('/register', [
        'name' => 'Workspace Owner',
        'email' => 'owner@example.test',
        'password' => 'a-strong-password-123',
        'password_confirmation' => 'a-strong-password-123',
    ]);

    $owner = User::query()->where('email', 'owner@example.test')->firstOrFail();
    $operator = User::factory()->create();
    $operator->roles()->attach(Role::query()->where('slug', 'admin')->value('id'));

    $this->actingAs($operator)
        ->post(route('users.store'), [
            'name' => 'Escalation attempt',
            'email' => 'escalation@example.test',
            'password' => 'a-strong-password-123',
            'password_confirmation' => 'a-strong-password-123',
            'role_id' => Role::query()->where('slug', 'super-admin')->value('id'),
        ])
        ->assertForbidden();

    $this->delete(route('users.destroy', $owner))->assertForbidden();
    $this->assertDatabaseMissing('users', ['email' => 'escalation@example.test']);
    $this->assertDatabaseHas('users', ['id' => $owner->id, 'deleted_at' => null]);
});

it('switches the persisted interface locale to Arabic and RTL', function () {
    $this->post('/register', [
        'name' => 'Workspace Owner',
        'email' => 'owner@example.test',
        'password' => 'a-strong-password-123',
        'password_confirmation' => 'a-strong-password-123',
    ]);

    $this->post(route('locale.update'), ['locale' => 'ar']);

    $this->get(route('dashboard'))
        ->assertOk()
        ->assertSee('lang="ar" dir="rtl"', false)
        ->assertSee('إجمالي المنتجات');
});

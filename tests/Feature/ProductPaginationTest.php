<?php

use App\Models\User;
use App\Models\Produk;
use App\Models\Kategori;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('pemilik can view products page normally', function () {
    $pemilik = User::factory()->create([
        'role' => User::ROLE_PEMILIK,
    ]);

    $response = $this->actingAs($pemilik)->get(route('produk.index'));

    $response->assertStatus(200);
    $response->assertViewIs('produk.index');
    $response->assertSee('Manajemen Produk');
});

test('pemilik can paginate products page via AJAX', function () {
    $pemilik = User::factory()->create([
        'role' => User::ROLE_PEMILIK,
    ]);

    // Create a category and some products to make sure pagination works
    $kategori = Kategori::create(['nama' => 'Test Kategori']);
    Produk::create([
        'nama' => 'Test Produk 1',
        'sku' => 'TST-PRD-1',
        'kategori_id' => $kategori->id,
        'harga' => 1000,
        'stok' => 10,
        'tipe_produk' => 'stock',
        'status' => true,
    ]);

    $response = $this->actingAs($pemilik)->get(route('produk.index'), [
        'X-Requested-With' => 'XMLHttpRequest',
    ]);

    $response->assertStatus(200);
    $response->assertViewIs('produk._table');
    $response->assertSee('Test Produk 1');
    $response->assertSee('TST-PRD-1');
});

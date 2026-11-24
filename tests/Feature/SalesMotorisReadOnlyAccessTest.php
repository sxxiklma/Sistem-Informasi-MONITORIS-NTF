<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Product;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SalesMotorisReadOnlyAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_sales_motoris_can_view_customers_list_with_only_view_action(): void
    {
        $sales = User::where('email', 'sales1@motoris.test')->first();
        $customer = Customer::first();

        $response = $this->actingAs($sales)->get(route('customers.index'));

        $response->assertOk();
        // Should contain view button
        $response->assertSee('viewCustomerDetail');
        // Should NOT contain Tambah Pelanggan button
        $response->assertDontSee('Tambah Pelanggan');
        // Should NOT contain Edit or Delete links/buttons
        $response->assertDontSee(route('customers.edit', $customer));
    }

    public function test_sales_motoris_cannot_create_or_modify_customers(): void
    {
        $sales = User::where('email', 'sales1@motoris.test')->first();
        $customer = Customer::first();

        // Cannot view create form
        $this->actingAs($sales)->get(route('customers.create'))->assertForbidden();

        // Cannot store
        $this->actingAs($sales)->post(route('customers.store'), [
            'name' => 'Outlet Baru',
            'code' => 'OUT-99999',
            'type' => 'retail',
        ])->assertForbidden();

        // Cannot edit
        $this->actingAs($sales)->get(route('customers.edit', $customer))->assertForbidden();

        // Cannot delete
        $this->actingAs($sales)->delete(route('customers.destroy', $customer))->assertForbidden();
    }

    public function test_sales_motoris_can_view_products_list_with_only_view_action(): void
    {
        $sales = User::where('email', 'sales1@motoris.test')->first();
        $product = Product::first();

        $response = $this->actingAs($sales)->get(route('products.index'));

        $response->assertOk();
        // Should contain view button
        $response->assertSee('viewProductDetail');
        // Should NOT contain Tambah Produk button
        $response->assertDontSee('Tambah Produk');
        // Should NOT contain Edit button
        if ($product) {
            $response->assertDontSee(route('products.edit', $product));
        }
    }

    public function test_sales_motoris_cannot_create_or_modify_products(): void
    {
        $sales = User::where('email', 'sales1@motoris.test')->first();
        $product = Product::first();

        // Cannot view create form
        $this->actingAs($sales)->get(route('products.create'))->assertForbidden();

        // Cannot store
        $this->actingAs($sales)->post(route('products.store'), [
            'name' => 'Produk Baru',
            'sku' => 'SKU-NEW',
            'price' => 10000,
            'unit' => 'pcs',
        ])->assertForbidden();

        if ($product) {
            // Cannot edit
            $this->actingAs($sales)->get(route('products.edit', $product))->assertForbidden();
            // Cannot delete
            $this->actingAs($sales)->delete(route('products.destroy', $product))->assertForbidden();
        }
    }

    public function test_admin_can_still_see_all_crud_actions_for_customers_and_products(): void
    {
        $admin = User::where('email', 'admin@motoris.test')->first();
        $customer = Customer::first();

        $customerResponse = $this->actingAs($admin)->get(route('customers.index'));
        $customerResponse->assertOk();
        $customerResponse->assertSee('Tambah Pelanggan');
        $customerResponse->assertSee('viewCustomerDetail');
        $customerResponse->assertSee(route('customers.edit', $customer));

        $productResponse = $this->actingAs($admin)->get(route('products.index'));
        $productResponse->assertOk();
        $productResponse->assertSee('Tambah Produk');
        $productResponse->assertSee('viewProductDetail');
    }
}

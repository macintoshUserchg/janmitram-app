<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Area;
use App\Models\Brand;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Shop;
use App\Models\Unit;
use App\Repositories\OrderRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ShopAllocationTest extends TestCase
{
    use RefreshDatabase;

    public function test_haversine_km_returns_expected_distance(): void
    {
        $this->assertSame(0.0, haversineKm(26.9, 75.8, 26.9, 75.8));

        // Delhi (28.6139, 77.2090) -> Jaipur (26.9124, 75.7873) ≈ 239 km
        $km = haversineKm(28.6139, 77.2090, 26.9124, 75.7873);
        $this->assertEqualsWithDelta(239.0, $km, 5.0);
    }

    private function masterWithTwoCopies(): array
    {
        $nearShop = Shop::factory()->create(['latitude' => 26.91, 'longitude' => 75.79, 'delivery_charge' => 20]);
        $farShop = Shop::factory()->create(['latitude' => 28.61, 'longitude' => 77.21, 'delivery_charge' => 80]);
        // shops factory must have an active user; ensure is_active on both users
        $nearShop->user->update(['is_active' => true]);
        $farShop->user->update(['is_active' => true]);

        // Factory prerequisites under RefreshDatabase (no seeders run):
        // ProductFactory needs a Brand row (Brand::all()->random()) and a Unit
        // (FK-constrained unit_id); AddressFactory needs a Customer (which needs
        // the 'customer' role) and an Area.
        Role::create(['name' => 'customer']);
        Customer::factory()->create();
        Area::factory()->create();
        Brand::create(['name' => 'Test Brand', 'slug' => 'test-brand']);
        $unit = Unit::create(['name' => 'kg', 'shop_id' => $nearShop->id, 'is_active' => true]);

        $master = Product::factory()->create([
            'shop_id' => $nearShop->id,
            'unit_id' => $unit->id,
            'quantity' => 10,
            'is_active' => true,
            'is_approve' => true,
        ]);
        $copy = Product::factory()->create([
            'shop_id' => $farShop->id,
            'master_product_id' => $master->id,
            'unit_id' => $unit->id,
            'quantity' => 10,
            'is_active' => true,
            'is_approve' => true,
        ]);

        return [$master, $copy, $nearShop, $farShop];
    }

    public function test_candidate_shops_are_ranked_by_distance(): void
    {
        [$master, $copy, $nearShop, $farShop] = $this->masterWithTwoCopies();
        $address = Address::factory()->create(['latitude' => 26.9, 'longitude' => 75.8]);

        $candidates = OrderRepository::candidateShopsForLine($master, 2, $address);

        $this->assertCount(2, $candidates);
        $this->assertSame($nearShop->id, $candidates[0]->shop_id);
        $this->assertSame($farShop->id, $candidates[1]->shop_id);
        $this->assertTrue($candidates[0]->radius_eligible);
        $this->assertFalse($candidates[1]->radius_eligible);
        $this->assertSame(20.0, $candidates[0]->delivery_charge);
    }

    public function test_allocate_nearest_shop_picks_in_radius_copy(): void
    {
        [$master, $copy, $nearShop, $farShop] = $this->masterWithTwoCopies();
        $address = Address::factory()->create(['latitude' => 26.9, 'longitude' => 75.8]);

        $allocated = $this->invokePrivate('allocateNearestShop', [$master, 2, $address]);

        $this->assertNotNull($allocated);
        $this->assertSame($nearShop->id, $allocated->shop_id);
    }

    public function test_allocate_honours_override_pick(): void
    {
        [$master, $copy, $nearShop, $farShop] = $this->masterWithTwoCopies();
        $address = Address::factory()->create(['latitude' => 26.9, 'longitude' => 75.8]);

        $allocated = $this->invokePrivate('allocateNearestShop', [$master, 2, $address, $farShop->id]);

        $this->assertNotNull($allocated);
        $this->assertSame($farShop->id, $allocated->shop_id);
    }

    private function invokePrivate(string $method, array $args)
    {
        $ref = new \ReflectionMethod(OrderRepository::class, $method);
        $ref->setAccessible(true);

        return $ref->invokeArgs(null, $args);
    }
}

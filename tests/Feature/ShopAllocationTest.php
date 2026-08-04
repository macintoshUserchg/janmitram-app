<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
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
}

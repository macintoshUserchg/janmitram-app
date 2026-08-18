<?php

namespace Tests\Feature;

use Tests\TestCase;

class OlaMapsIntegrationTest extends TestCase
{
    /**
     * Test map client configuration endpoint.
     */
    public function test_maps_config_endpoint_returns_valid_structure(): void
    {
        $response = $this->getJson('/api/maps/config');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'tiles_url',
                    'base_url',
                    'default_center' => ['lat', 'lng'],
                    'default_zoom',
                    'has_api_key',
                ],
            ]);
    }

    /**
     * Test autocomplete endpoint returns results.
     */
    public function test_maps_autocomplete_endpoint_returns_results(): void
    {
        $response = $this->getJson('/api/maps/autocomplete?input=Jaipur&limit=3');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data',
            ]);
    }

    /**
     * Test reverse geocoding parameter validation.
     */
    public function test_maps_reverse_geocode_validates_parameters(): void
    {
        $response = $this->getJson('/api/maps/reverse-geocode');

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['lat', 'lng']);
    }

    /**
     * Test reverse geocoding with coordinates.
     */
    public function test_maps_reverse_geocode_with_coordinates(): void
    {
        $response = $this->getJson('/api/maps/reverse-geocode?lat=26.9124&lng=75.7873');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
            ]);
    }

    /**
     * Test directions route endpoint.
     */
    public function test_maps_directions_endpoint_returns_route_geometry(): void
    {
        $response = $this->getJson('/api/maps/directions?origin_lat=26.9124&origin_lng=75.7873&dest_lat=26.8000&dest_lng=75.8000');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'success',
                    'geometry',
                ],
            ]);
    }
}

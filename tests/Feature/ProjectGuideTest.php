<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ProjectGuideTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
        $this->seed(PermissionSeeder::class);
    }

    private function rootUser(): User
    {
        $user = User::factory()->create(['is_active' => true]);
        $user->assignRole(Role::findOrCreate('root', 'web'));

        return $user;
    }

    public function test_project_guide_route_returns_ok(): void
    {
        $response = $this->actingAs($this->rootUser())
            ->get(route('admin.project-guide.index'));

        $response->assertOk();
        $response->assertSee('Janmitram Master Project Guide');
    }
}

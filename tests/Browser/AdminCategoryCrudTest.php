<?php

namespace Tests\Browser;

use App\Models\Category;
use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class AdminCategoryCrudTest extends DuskTestCase
{
    private static string $testName = 'Test Category Dusk';

    private static string $updatedName = 'Updated Category Dusk';

    /**
     * Create a new category (requires name + thumbnail).
     */
    public function test_create_category(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::role('root')->first())
                ->visit('/admin/category/create')
                ->assertSee('Create New Category')
                ->type('name', self::$testName)
                ->type('description', 'Created by Dusk test')
                ->attach('thumbnail', __DIR__.'/test-image.png')
                ->press('Submit')
                ->waitForText('Category created successfully', 15);

            $category = Category::where('name', self::$testName)->first();
            $this->assertNotNull($category);
            $this->assertEquals('Created by Dusk test', $category->description);
        });
    }

    /**
     * Category list page loads and the record exists in the database.
     */
    public function test_category_list_shows_new_category(): void
    {
        $category = Category::where('name', self::$testName)->first();
        $this->assertNotNull($category);

        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::role('root')->first())
                ->visit('/admin/category')
                ->assertSee('Category List');
        });
    }

    /**
     * Edit category name and description.
     */
    public function test_update_category(): void
    {
        $this->browse(function (Browser $browser) {
            $category = Category::where('name', self::$testName)->first();
            $this->assertNotNull($category);

            $browser->loginAs(User::role('root')->first())
                ->visit("/admin/category/{$category->id}/edit")
                ->assertSee('Edit Category')
                ->type('name', self::$updatedName)
                ->type('description', 'Updated by Dusk test')
                ->press('Update')
                ->waitForText('Category updated successfully', 15);

            $category->refresh();
            $this->assertEquals(self::$updatedName, $category->name);
            $this->assertEquals('Updated by Dusk test', $category->description);
        });
    }

    /**
     * Toggle category status (active/inactive) by visiting the toggle URL directly.
     */
    public function test_toggle_category_status(): void
    {
        $category = Category::where('name', self::$updatedName)->first();
        $this->assertNotNull($category);
        $originalStatus = (bool) $category->status;

        $this->browse(function (Browser $browser) use ($category) {
            $browser->loginAs(User::role('root')->first())
                ->visit('/admin/category')
                ->visit("/admin/category/{$category->id}/toggle")
                ->waitForText('Status updated successfully', 10);
        });

        $category->refresh();
        $this->assertNotEquals($originalStatus, (bool) $category->status,
            'Category status should have toggled');
    }

    /**
     * Cleanup: remove the test category.
     */
    public function test_cleanup_category(): void
    {
        $category = Category::where('name', self::$updatedName)->first();
        if ($category) {
            $category->delete();
        }

        $this->assertNull(Category::where('name', self::$updatedName)->first());
    }
}

<?php

namespace Tests\Browser;

use App\Models\Category;
use App\Models\SubCategory;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class AdminSubCategoryCrudTest extends DuskTestCase
{
    use Helpers;

    private static string $testName = 'Test SubCategory Dusk';

    private static string $updatedName = 'Updated SubCategory Dusk';

    public function test_create_sub_category(): void
    {
        // Ensure we have an active category the form can display
        $category = Category::first();
        $this->assertNotNull($category);
        $category->update(['status' => 1]);

        $this->browse(function (Browser $browser) use ($category) {
            $browser->loginAs($this->admin())
                ->visit('/admin/subcategory/create')
                ->assertSee('Create New Sub Category');

            $browser->script([
                "$('select[name=\"category[]\"]').val(['{$category->id}']).trigger('change');",
            ]);

            $browser->type('name', self::$testName)
                ->attach('thumbnail', $this->fakeImage())
                ->press('Submit')
                ->waitForText('Sub Category created successfully', 15);

            $record = SubCategory::where('name', self::$testName)->first();
            $this->assertNotNull($record);
        });
    }

    public function test_sub_category_list_shows_new(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin())
                ->visit('/admin/subcategory')
                ->assertSee('Sub Category');
        });
    }

    public function test_update_sub_category(): void
    {
        $this->browse(function (Browser $browser) {
            $record = SubCategory::where('name', self::$testName)->first();
            $this->assertNotNull($record);

            $browser->loginAs($this->admin())
                ->visit("/admin/subcategory/{$record->id}/edit")
                ->assertSee('Edit Sub Category')
                ->type('name', self::$updatedName)
                ->press('Update')
                ->waitForText('Sub Category updated successfully', 10);

            $record->refresh();
            $this->assertEquals(self::$updatedName, $record->name);
        });
    }

    public function test_toggle_sub_category_status(): void
    {
        $record = SubCategory::where('name', self::$updatedName)->first();
        $this->assertNotNull($record);
        $originalStatus = (bool) $record->is_active;

        $this->browse(function (Browser $browser) use ($record) {
            $browser->loginAs($this->admin())
                ->visit('/admin/subcategory')
                ->visit("/admin/subcategory/{$record->id}/toggle")
                ->waitForText('Status updated successfully', 10);
        });

        $record->refresh();
        $this->assertNotEquals($originalStatus, (bool) $record->is_active);
    }

    public function test_cleanup_sub_category(): void
    {
        SubCategory::where('name', self::$updatedName)->delete();
        $this->assertNull(SubCategory::where('name', self::$updatedName)->first());
    }
}

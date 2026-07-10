<?php

namespace Tests\Browser;

use App\Models\Blog;
use App\Models\Category;
use App\Models\Page;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class AdminBlogPageTest extends DuskTestCase
{
    use Helpers;

    public function test_create_blog(): void
    {
        $this->browse(function (Browser $browser) {
            $category = Category::where('status', 1)->first();
            $this->assertNotNull($category);

            $browser->loginAs($this->admin())
                ->visit('/admin/blog/create')
                ->assertSee('Add New Blog')
                ->type('title', 'Test Blog Dusk')
                ->attach('thumbnail', $this->fakeImage());

            $this->selectByValue($browser, 'select[name="category"]', $category->id);
            $this->fillQuill($browser, '<p>Test blog content</p>');

            $browser->press('Submit')
                ->waitForText('Created successfully', 10);

            $record = Blog::where('title', 'Test Blog Dusk')->first();
            $this->assertNotNull($record);
        });
    }

    public function test_blog_list_shows_new_blog(): void
    {
        $record = Blog::where('title', 'Test Blog Dusk')->first();
        $this->assertNotNull($record);

        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin())
                ->visit('/admin/blog')
                ->waitForText('Test Blog Dusk', 10);
        });
    }

    public function test_toggle_blog(): void
    {
        $record = Blog::where('title', 'Test Blog Dusk')->first();
        $this->assertNotNull($record);
        $originalStatus = (bool) $record->is_active;

        $this->browse(function (Browser $browser) use ($record) {
            $browser->loginAs($this->admin())
                ->visit('/admin/blog')
                ->visit("/admin/blog/{$record->id}/toggle")
                ->waitForText('Status Updated Successfully', 10);
        });

        $record->refresh();
        $this->assertNotEquals($originalStatus, (bool) $record->is_active);
    }

    public function test_create_page(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin())
                ->visit('/admin/page/create')
                ->assertSee('Add New Page')
                ->type('title', 'Test Page Dusk');

            $this->fillQuill($browser, '<p>Test page content</p>');

            $browser->press('Submit')
                ->waitForText('created successfully', 10);

            $record = Page::where('title', 'Test Page Dusk')->first();
            $this->assertNotNull($record);
        });
    }

    public function test_page_list_shows_new_page(): void
    {
        $record = Page::where('title', 'Test Page Dusk')->first();
        $this->assertNotNull($record);

        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin())
                ->visit('/admin/page')
                ->waitForText('Test Page Dusk', 10);
        });
    }

    public function test_cleanup(): void
    {
        Blog::where('title', 'Test Blog Dusk')->forceDelete();
        Page::where('title', 'Test Page Dusk')->forceDelete();
        $this->assertNull(Blog::where('title', 'Test Blog Dusk')->first());
        $this->assertNull(Page::where('title', 'Test Page Dusk')->first());
    }
}

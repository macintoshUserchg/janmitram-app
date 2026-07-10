<?php

namespace Tests\Browser;

use App\Models\User;
use Laravel\Dusk\Browser;

trait Helpers
{
    protected array $cleanupIds = [];

    protected function admin(): User
    {
        return User::role('root')->first();
    }

    protected function fakeImage(): string
    {
        return __DIR__ . '/test-image.png';
    }

    protected function selectByValue(Browser $browser, string $selector, string|int $value): void
    {
        $browser->script([
            "$('{$selector}').val('{$value}').trigger('change');",
            "$('{$selector}').trigger('select2:select');",
        ]);
    }

    protected function fillQuill(Browser $browser, string $html): void
    {
        $escaped = addslashes($html);
        $browser->script([
            "document.querySelector('.ql-editor').innerHTML = '{$escaped}';",
            "document.querySelector('.ql-editor').dispatchEvent(new Event('input', {bubbles: true}));",
        ]);
    }

    protected function tearDown(): void
    {
        foreach ($this->cleanupIds as [$model, $ids]) {
            $ids = (array) $ids;
            if (! empty($ids)) {
                $model::whereIn('id', $ids)->forceDelete();
            }
        }
        parent::tearDown();
    }
}

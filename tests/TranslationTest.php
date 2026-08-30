<?php

namespace Tests;

class TranslationTest extends TestCase
{
    /** @test */
    public function it_has_english_common_translations(): void
    {
        $keys = [
            'url', 'address', 'email', 'mail', 'status', 'id', 'user_id',
            'name', 'username', 'save', 'cancel', 'active', 'inactive',
            'password', 'is_active', 'create', 'edit', 'site', 'website',
            'created_at', 'updated_at', 'created_by', 'deleted_at',
            'is_default', 'indefault', 'title', 'user', 'item', 'delete',
            'actions', 'allowed_ip', 'manage',
        ];

        foreach ($keys as $key) {
            $translation = trans("laramina::adminUI.common.{$key}", [], 'en');
            $this->assertNotEquals(
                "laramina::adminUI.common.{$key}",
                $translation,
                "English translation missing for key: {$key}"
            );
        }
    }

    /** @test */
    public function it_has_persian_common_translations(): void
    {
        $keys = [
            'url', 'address', 'email', 'mail', 'status', 'id', 'user_id',
            'name', 'username', 'save', 'cancel', 'active', 'inactive',
            'password', 'is_active', 'create', 'edit',
        ];

        foreach ($keys as $key) {
            $translation = trans("laramina::adminUI.common.{$key}", [], 'fa');
            $this->assertNotEquals(
                "laramina::adminUI.common.{$key}",
                $translation,
                "Persian translation missing for key: {$key}"
            );
        }
    }

    /** @test */
    public function it_has_persian_module_translations(): void
    {
        $modules = ['credentials', 'socials', 'users', 'roles', 'permissions'];

        foreach ($modules as $module) {
            $title = trans("laramina::adminUI.modules.{$module}.fields.title", [], 'fa');
            $this->assertNotEquals(
                "laramina::adminUI.modules.{$module}.fields.title",
                $title,
                "Persian module title missing for: {$module}"
            );
        }
    }

    /** @test */
    public function it_has_english_and_persian_common_key_pairs(): void
    {
        $keys = ['name', 'email', 'status', 'active', 'inactive', 'create', 'edit', 'delete', 'save', 'cancel'];

        foreach ($keys as $key) {
            $en = trans("laramina::adminUI.common.{$key}", [], 'en');
            $fa = trans("laramina::adminUI.common.{$key}", [], 'fa');

            $this->assertNotEquals($en, $fa, "EN and FA translations should differ for: {$key}");
            $this->assertNotEmpty($en, "EN translation is empty for: {$key}");
            $this->assertNotEmpty($fa, "FA translation is empty for: {$key}");
        }
    }
}

<?php

namespace AdminPlatform;

use Illuminate\Support\ServiceProvider;

use AdminPlatform\Console\Commands\MakeAdminUI;



class AdminPlatformServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // انتشار کانفیگ
        $this->publishes([
            __DIR__ . '/../config/admin-platform.php' => config_path('admin-platform.php'),
        ], 'admin-platform-config');

        // انتشار assets (JS)
        $this->publishes([
            __DIR__ . '/../resources/js' => public_path('js'),
        ], 'admin-platform-assets');

        // انتشار views (اختیاری)
        $this->publishes([
            __DIR__ . '/../resources/views' => resource_path('views/vendor/admin-platform'),
        ], 'admin-platform-views');

        // بارگذاری دستورات
        if ($this->app->runningInConsole()) {
            $this->commands([
                MakeAdminUI::class,
            ]);
        }
        // انتشار فایل زبان (هر دو زبان)
        $this->publishes([
            __DIR__ . '/../resources/lang/fa' => resource_path('lang/vendor/admin-platform/fa'),
            __DIR__ . '/../resources/lang/en' => resource_path('lang/vendor/admin-platform/en'),
        ], 'admin-platform-lang');
        
        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'admin-platform');


        // بارگذاری views (اگر می‌خواهید از ویوهای پکیج استفاده کنید)
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'admin-platform');
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/admin-platform.php', 'admin-platform');
    }
}

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
        // بارگذاری فایل زبان 

        $this->publishes([
            __DIR__ . '/../resources/lang' => resource_path('lang/vendor/admin-platform'),
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

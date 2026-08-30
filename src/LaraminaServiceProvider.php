<?php

namespace Laramina;

use Illuminate\Support\ServiceProvider;

use Laramina\Console\Commands\MakeAdminUI;



class LaraminaServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // انتشار کانفیگ
        $this->publishes([
            __DIR__ . '/../config/laramina.php' => config_path('laramina.php'),
        ], 'laramina-config');

        // انتشار assets (JS)
        $this->publishes([
            __DIR__ . '/../resources/js' => public_path('js'),
        ], 'laramina-assets');

        // انتشار views (اختیاری)
        $this->publishes([
            __DIR__ . '/../resources/views' => resource_path('views/vendor/laramina'),
        ], 'laramina-views');

        // بارگذاری دستورات
        if ($this->app->runningInConsole()) {
            $this->commands([
                MakeAdminUI::class,
            ]);
        }
        // انتشار فایل زبان (هر دو زبان)
        $this->publishes([
            __DIR__ . '/../resources/lang/fa' => resource_path('lang/vendor/laramina/fa'),
            __DIR__ . '/../resources/lang/en' => resource_path('lang/vendor/laramina/en'),
        ], 'laramina-lang');
        
        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'laramina');


        // بارگذاری views (اگر می‌خواهید از ویوهای پکیج استفاده کنید)
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'laramina');
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/laramina.php', 'laramina');
    }
}

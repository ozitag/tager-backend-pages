<?php

namespace OZiTAG\Tager\Backend\Pages;

use Illuminate\Support\ServiceProvider;
use OZiTAG\Tager\Backend\Pages\Enums\PageScope;
use OZiTAG\Tager\Backend\Panel\TagerPanel;
use OZiTAG\Tager\Backend\Rbac\TagerScopes;

class PagesServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__ . '/../routes/routes.php');

        $this->loadMigrationsFrom(__DIR__ . '/../migrations');

        $this->publishes([
            __DIR__ . '/../config.php' => config_path('tager-pages.php'),
        ]);

        TagerPanel::registerRouteHandler('.*', PagesPanelRouteHandler::class);

        TagerScopes::registerGroup('Pages', [
            PageScope::View => 'View Pages',
            PageScope::Create => 'Create Pages',
            PageScope::Edit => 'Edit Pages',
            PageScope::Delete => 'Delete Pages'
        ]);
    }
}

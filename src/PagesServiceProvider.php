<?php

namespace OZiTAG\Tager\Backend\Pages;

use Illuminate\Support\ServiceProvider;
use OZiTAG\Tager\Backend\Pages\Enums\PageScope;
use OZiTAG\Tager\Backend\Panel\TagerPanel;
use OZiTAG\Tager\Backend\Rbac\TagerScopes;
use OZiTAG\Tager\Backend\Seo\Structures\ParamsTemplate;
use OZiTAG\Tager\Backend\Seo\TagerSeo;

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

        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'tager-pages');
        $this->loadMigrationsFrom(__DIR__ . '/../migrations');

        $this->publishes([
            __DIR__ . '/../config.php' => config_path('tager-pages.php'),
        ]);

        TagerPanel::registerRouteHandler('.*', PagesPanelRouteHandler::class);

        TagerSeo::registerSitemapHandler(PagesSitemapHandler::class);

        TagerSeo::registerParamsTemplate('page', new ParamsTemplate(
            __('tager-pages::seo-template.title'), [
            'title' => __('tager-pages::seo-template.field_title'),
            'excerpt' => __('tager-pages::seo-template.field_excerpt')
        ], false, '{{title}}', '{{excerpt}}'), '/tager/pages');

        TagerScopes::registerGroup(__('tager-pages::scopes.group'), [
            PageScope::View->value => __('tager-pages::scopes.view_pages'),
            PageScope::Create->value => __('tager-pages::scopes.create_pages'),
            PageScope::Edit->value => __('tager-pages::scopes.edit_pages'),
            PageScope::Delete->value => __('tager-pages::scopes.delete_pages')
        ]);
    }
}

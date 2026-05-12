<?php
namespace EnzanRocket\Foundation\Providers;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Illuminate\Support\Facades\Auth;
use EnzanRocket\Foundation\Auth\EloquentUserProvider;
use EnzanRocket\Foundation\Console\Commands\ExportTableToFile;
use EnzanRocket\Foundation\Console\Commands\ImportFileToTable;
use EnzanRocket\Foundation\Console\Commands\SetAppName;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        /* Auth */
        Auth::provider('rocket-eloquent', function ($app, array $config) {
            return new EloquentUserProvider($app['hash'], $config['model']);
        });
    }

    /**
     * Register any application services.
     */
    public function register()
    {
        /* Services */
        $this->app->singleton(
            \EnzanRocket\Foundation\Services\MailServiceInterface::class,
            \EnzanRocket\Foundation\Services\Production\MailService::class
        );

        $this->app->singleton(
            \EnzanRocket\Foundation\Services\ImageServiceInterface::class,
            \EnzanRocket\Foundation\Services\Production\ImageService::class
        );

        $this->app->singleton(
            \EnzanRocket\Foundation\Services\LanguageServiceInterface::class,
            \EnzanRocket\Foundation\Services\Production\LanguageService::class
        );

        $this->app->singleton(
            \EnzanRocket\Foundation\Services\SlackServiceInterface::class,
            \EnzanRocket\Foundation\Services\Production\SlackService::class
        );

        $this->app->singleton(
            \EnzanRocket\Foundation\Services\FileUploadServiceInterface::class,
            \EnzanRocket\Foundation\Services\Production\FileUploadService::class
        );

        $this->app->singleton(
            \EnzanRocket\Foundation\Services\FileUploadS3ServiceInterface::class,
            \EnzanRocket\Foundation\Services\Production\FileUploadS3Service::class
        );

        $this->app->singleton(
            \EnzanRocket\Foundation\Services\FileUploadLocalServiceInterface::class,
            \EnzanRocket\Foundation\Services\Production\FileUploadLocalService::class
        );

        /* Helpers */
        $this->app->singleton(
            \EnzanRocket\Foundation\Helpers\DateTimeHelperInterface::class,
            \EnzanRocket\Foundation\Helpers\Production\DateTimeHelper::class
        );

        $this->app->singleton(
            \EnzanRocket\Foundation\Helpers\LocaleHelperInterface::class,
            \EnzanRocket\Foundation\Helpers\Production\LocaleHelper::class
        );

        $this->app->singleton(
            \EnzanRocket\Foundation\Helpers\URLHelperInterface::class,
            \EnzanRocket\Foundation\Helpers\Production\URLHelper::class
        );

        $this->app->singleton(
            \EnzanRocket\Foundation\Helpers\CollectionHelperInterface::class,
            \EnzanRocket\Foundation\Helpers\Production\CollectionHelper::class
        );

        $this->app->singleton(
            \EnzanRocket\Foundation\Helpers\StringHelperInterface::class,
            \EnzanRocket\Foundation\Helpers\Production\StringHelper::class
        );

        $this->app->singleton(
            \EnzanRocket\Foundation\Helpers\PaginationHelperInterface::class,
            \EnzanRocket\Foundation\Helpers\Production\PaginationHelper::class
        );

        $this->app->singleton(
            \EnzanRocket\Foundation\Helpers\TypeHelperInterface::class,
            \EnzanRocket\Foundation\Helpers\Production\TypeHelper::class
        );

        $this->app->singleton(
            \EnzanRocket\Foundation\Helpers\RedirectHelperInterface::class,
            \EnzanRocket\Foundation\Helpers\Production\RedirectHelper::class
        );

        $this->app->singleton(
            \EnzanRocket\Foundation\Helpers\DataHelperInterface::class,
            \EnzanRocket\Foundation\Helpers\Production\DataHelper::class
        );

        $this->app->singleton(
            \EnzanRocket\Foundation\Helpers\FileHelperInterface::class,
            \EnzanRocket\Foundation\Helpers\Production\FileHelper::class
        );

        $this->app->singleton(
            \EnzanRocket\Foundation\Helpers\ArrayHelperInterface::class,
            \EnzanRocket\Foundation\Helpers\Production\ArrayHelper::class
        );

        $this->app->singleton(
            \EnzanRocket\Foundation\Services\ExportServiceInterface::class,
            \EnzanRocket\Foundation\Services\Production\ExportService::class
        );

        //Commands
        $this->app->singleton('command.rocket.export.table', function($app) {
            return new ExportTableToFile($app['files']);
        });

        $this->app->singleton('command.rocket.import.file', function($app) {
            return new ImportFileToTable($app['files']);
        });

        $this->app->singleton('command.rocket.set.name', function($app) {
            return new SetAppName($app['files']);
        });

        $this->commands('command.rocket.export.table', 'command.rocket.import.file', 'command.rocket.set.name');
    }
}

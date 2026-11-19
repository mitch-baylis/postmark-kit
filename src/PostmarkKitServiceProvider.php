<?php

namespace MitchBaylis\PostmarkKit;

use Illuminate\Mail\Events\MessageSending;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use MitchBaylis\PostmarkKit\Console\TestCommand;
use MitchBaylis\PostmarkKit\Http\PostmarkWebhookController;
use MitchBaylis\PostmarkKit\Suppression\SuppressionGuard;
use MitchBaylis\PostmarkKit\Suppression\SuppressionList;

class PostmarkKitServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/postmark.php', 'postmark');

        $this->app->singleton(SuppressionList::class);
        $this->app->singleton(PostmarkMailer::class);
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'postmark-kit');
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        $this->publishes([
            __DIR__.'/../config/postmark.php' => config_path('postmark.php'),
        ], 'postmark-config');

        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/postmark-kit'),
        ], 'postmark-views');

        $this->registerWebhookRoute();
        $this->registerSuppressionGuard();

        if ($this->app->runningInConsole()) {
            $this->commands([TestCommand::class]);
        }
    }

    private function registerWebhookRoute(): void
    {
        if (! config('postmark.webhook.enabled')) {
            return;
        }

        Route::post(config('postmark.webhook.path'), PostmarkWebhookController::class)
            ->middleware(config('postmark.webhook.middleware', ['api']))
            ->name('postmark.webhook');
    }

    private function registerSuppressionGuard(): void
    {
        if (! config('postmark.suppress_recipients')) {
            return;
        }

        Event::listen(
            MessageSending::class,
            [SuppressionGuard::class, 'handle'],
        );
    }
}

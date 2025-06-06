<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Auth as FirebaseAuth;
use Kreait\Firebase\Contract\Database;

class FirebaseServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(FirebaseAuth::class, function ($app) {
            $factory = (new Factory)
                ->withServiceAccount(config('firebase.credentials'))
                ->withDatabaseUri(config('firebase.database_url'));

            return $factory->createAuth();
        });

        $this->app->singleton(Database::class, function ($app) {
            $factory = (new Factory)
                ->withServiceAccount(config('firebase.credentials'))
                ->withDatabaseUri(config('firebase.database_url'));

            return $factory->createDatabase();
        });
    }

    public function boot()
    {
        //
    }
}
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
                ->withServiceAccount(storage_path('app/smartcab-8bb42-firebase-adminsdk-fbsvc-de33a8e45b.json'))
                ->withDatabaseUri(env('FIREBASE_DATABASE_URL'));

            return $factory->createAuth();
        });

        $this->app->singleton(Database::class, function ($app) {
            $factory = (new Factory)
                ->withServiceAccount(storage_path('app/smartcab-8bb42-firebase-adminsdk-fbsvc-de33a8e45b.json'))
                ->withDatabaseUri(env('FIREBASE_DATABASE_URL'));

            return $factory->createDatabase();
        });
    }

    public function boot()
    {
        //
    }
}

<?php

return [
    'database_url' => env('FIREBASE_DATABASE_URL', 'https://smartcab-8bb42-default-rtdb.firebaseio.com'),
    'project_id' => env('FIREBASE_PROJECT_ID', 'smartcab-8bb42'),
    'credentials' => storage_path('app/smartcab-8bb42-firebase-adminsdk-fbsvc-de33a8e45b.json'),
];
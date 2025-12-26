<?php

return [

    // Nome univoco per questa applicazione nel cluster
    'client_name' => env('DKV_CLIENT_NAME', env('APP_NAME', 'app1')),

    // Token condiviso per autenticare le richieste tra client
    'auth_token' => env('DKV_AUTH_TOKEN', 'change-me'),

    // Lista iniziale dei client partecipanti
    'clients' => [
        // 'app1' => 'https://app1.example.com',
        // 'app2' => 'https://app2.example.com',
    ],

    // Endpoint base per le API del sync (sul singolo client)
    'base_path' => '/api/dkv',

    // Strategia di risoluzione conflitti: 'last_write_wins' per ora
    'conflict_strategy' => 'last_write_wins',
];

Laravel-ArubaPec
================

Laravel-ArubaPec è una semplice (e, al momento, incompleta) libreria Laravel per l'interrogazione delle API di ArubaPec. Permette di verificare se un'anagrafica o un'email sono già state create, e permette di creare anagrafiche ed email.

Installazione
-------------

    composer require twobeesolution/laravel-arubapec
    php artisan vendor:publish --provider='TwoBeeSolution\ArubaPec\ArubaPecServiceProvider'

Aggiungere a `config/app.php` nell'array `providers`:

    TwoBeeSolution\ArubaPec\ArubaPecServiceProvider::class,

e nell'array `aliases`

    'ArubaPec' => TwoBeeSolution\ArubaPec\ArubaPecWsClient::class,

Utilizzo
--------
[in progress]
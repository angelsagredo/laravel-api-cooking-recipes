# laravel-api-cooking-recipes
> Laravel 5.6 API example implementing JWT as auth method.

### [Live demo - https://comidasaludable.top](https://comidasaludable.top)

<p align="center">
<img src="https://i.imgur.com/QegONcG.jpg">
</p>

## **Heads up!** This project is splitted into two parts. See **[vue-spa-cooking-recipes](https://github.com/angelsagredo/vue-spa-cooking-recipes)** for SPA web that consume this API.

## Features

- Laravel 5.6
- Login and register
- Authentication with JWT

## Installation

    git clone https://github.com/angelsagredo/laravel-api-cooking-recipes.git 
    cd laravel-api-cooking-recipes  
    composer install
        
    - Open .env file and edit with your own custom settings and credentials.  

    php artisan key:generate
    php artisan jwt:secret 
    php artisan migrate

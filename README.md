ABOUT THE PROJECT

## Project Title

Wallet Management - A Simple Wallet Management Application

## Setup Instructions

- Clone the repo or unzip the project zip.
- Open the project in your favorite IDE (e.g., VSCode, PHPSTORM).
- Make sure you have PHP, Composer, Node.js, and a MySQL database installed on your machine.
- Make sure your web server (e.g., Apache, Nginx) or laragon, xampp is running.
- Go to the project directory.
- Open the terminal and run `composer install` to install dependencies.
- Copy `.env.example` to `.env` and configure your database settings.
- Open the terminal and run `php artisan key:generate` to generate the application key.
- Run `php artisan migrate` to set up the database tables.
- Run `php artisan db:seed` to populate the database with initial data.
- Start the development server with `php artisan serve`.
- Open your postman and test the APIs.
- Base URL: `http://localhost:8000/api/` or whatever port your server is running on.
- Use the provided postman collection to test the APIs. https://documenter.getpostman.com/view/23411964/2sBXVhDAaK



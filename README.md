# Laravel Project Setup Guide

This guide will walk you through setting up the necessary configurations for Laravel Passport, Swagger, migrations, and seeding to populate your database with data.

## Requirements


- PHP 8.0 or later
- Composer
- MySQL or any other database of your choice

## Step 1: Install Laravel Passport

To set up Laravel Passport for API authentication:

Install Passport using Composer

Run the Passport installation command

Run the database migrations

## Step 2: Install Swagger

To set up Swagger for API documentation:

Run the Swagger generation command after setup

## Required Commands

composer update  
php artisan passport:install  
php artisan migrate 
php artisan db:seeds   
php artisan l5-swagger:generate 

## RolesTableSeeder
 php artisan db:seed --class=RolesTableSeeder

## swagger Api url

http://127.0.0.1:8000/api/documentation




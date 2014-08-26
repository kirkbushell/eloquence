# Eloquence

Eloquence is a package to extend Laravel 4's base Eloquent models and functionality.

It allows developers to continue using the PSR-0 standard when dealing with database field names in models. This package will be built on in the future to add more features to the great Eloquent library.


## Installation

Install the package via composer:

    composer require kirkbushell/eloquence 1.1.x

## Usage

First, add the eloquence service provider to your config/app.php file:

    'Eloquence\EloquenceServiceProvider',

Now, update your Eloquent alias to instead point to the Eloquence model version:

    'Eloquent' => 'Eloquence\Database\Model',

You should now be good to go with your models.

## Options

The default behaviour for Eloquence is to override the default way to access attributes in your models. This is done like so:

    public $enforceCamelCase = true;

You can change this if you need certain models to not use this behaviour, simply by setting it to false:

    public $enforceCamelCase = false;

You can also, if necessary - use the CamelCaseModel trait, instead of extending the Model class directly (which now just uses the new trait).

## Note!

It should be noted that Eloquence DOES NOT CHANGE how you write your schema migrations. You should still be using snake_case when setting up your fields and tables in your database schema migrations. This is a good thing - snake_case of field names is the defacto standard within the Laravel community :)


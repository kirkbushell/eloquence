# Eloquence

Eloquence is a package to extend Laravel 4's base Eloquent models and functionality.

It allows developers to continue using the PSR-0 standard when dealing with database field names in models. This package will be built on in the future to add more features to the great Eloquent library.

## Changelog

#### 1.1.0

* UUIDModel trait added
* CamelCaseModel trait added
* Model class updated to use CamelCaseModel trait - deprecated, backwards-compatibility support only
* Eloquence now its own namespace

#### 1.0.1

* Fixed an issue with dependency resolution

#### 1.0.0

* Initial implementation
* Camel casing of model attributes now available for both setters and getters

## Installation

Install the package via composer:

    composer require kirkbushell/eloquence 1.1.x

## Usage

First, add the eloquence service provider to your config/app.php file:

    'Eloquence\EloquenceServiceProvider',

It's important to note that this will automatically re-bind the Model class
that Eloquent uses for many-to-many relationships. This is necessary because
when the Pivot model is instantiated, we need it to utilise the parent model's
information and traits that may be needed.

You should now be good to go with your models.

## Options

The default behaviour for Eloquence is to override the default way to access attributes in your models. This is done like so:

    public $enforceCamelCase = true;

You can change this if you need certain models to not use this behaviour, simply by setting it to false:

    public $enforceCamelCase = false;

You can also, if necessary - use the CamelCaseModel trait, instead of extending the Model class directly (which now just uses the new trait).

## Note!

It should be noted that Eloquence DOES NOT CHANGE how you write your schema migrations. You should still be using snake_case when setting up your fields and tables in your database schema migrations. This is a good thing - snake_case of field names is the defacto standard within the Laravel community :)


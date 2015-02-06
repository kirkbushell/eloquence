# Eloquence

[![Latest Stable Version](https://poser.pugx.org/kirkbushell/eloquence/v/stable.svg)](https://packagist.org/packages/kirkbushell/eloquence) [![Total Downloads](https://poser.pugx.org/kirkbushell/eloquence/downloads.svg)](https://packagist.org/packages/kirkbushell/eloquence) [![Latest Unstable Version](https://poser.pugx.org/kirkbushell/eloquence/v/unstable.svg)](https://packagist.org/packages/kirkbushell/eloquence)

Eloquence is a package to extend Laravel 5's base Eloquent models and functionality.

It allows developers to continue using the PSR-0 standard when dealing with database field names in models. This package will be built on in the future to add more features to the great Eloquent library.

## Installation

Install the package via composer:

    composer require kirkbushell/eloquence ~1.2

For Laravel 4, please install the 1.1.4 release:

    composer require kirkbushell/eloquence 1.1.4

## Usage

First, add the eloquence service provider to your config/app.php file:

    'Eloquence\EloquenceServiceProvider',

It's important to note that this will automatically re-bind the Model class
that Eloquent uses for many-to-many relationships. This is necessary because
when the Pivot model is instantiated, we need it to utilise the parent model's
information and traits that may be needed.

You should now be good to go with your models.

## Camel case all the things!

For those of us who prefer to work with a single coding standard right across our applications, using the CamelCaseModel trait
will ensure that all those attributes, relationships and associated data from our Eloquent models persist through to our APIs
in a camel-case manner. This is important if you are writing front-end applications, which are also using camelCase. This allows
for a better standard across our application. To use:

    use Eloquence\Database\Traits\CamelCaseModel;

Put the above line in your models and that's it.

### Note!

Eloquence DOES NOT CHANGE how you write your schema migrations. You should still be using snake_case when setting up your fields 
and tables in your database schema migrations. This is a good thing - snake_case of field names is the defacto standard within 
the Laravel community :)


## UUIDs

Eloquence comes bundled with UUID capabilities that you can use in your models.

Simply include the UUIDModel trait:

    use Eloquence\Database\Traits\UUIDModel;

This will turn off id auto-incrementing in your model, and instead automatically generate a UUID4 value for your id field. One 
benefit of this is that you can actually know the id of your record BEFORE it's saved!

You must ensure that your id column is setup to handle UUID values. This can be done by creating a migration with the following 
properties:

    $table->char('id', $length = 36)->index();

It's important to note that you should do your research before using UUID functionality and whether it works for you. UUID 
field searches are much slower than indexed integer fields (such as autoincrement id fields).


### Custom UUIDs

Should you need a custom UUID solution (aka, maybe you don't want to use a UUID4 id), you can simply define the value you wish on 
the id field. The UUID model trait will not set the id if it has already been defined.


## Changelog

#### 1.2.0

* Laravel 5 support
* Readme updates

#### 1.1.4

* UUID fix

#### 1.1.3

* Removed the schema binding on the service provider

#### 1.1.2

* Removed the uuid column creation via custom blueprint

#### 1.1.1

* Dependency bug fix

#### 1.1.0

* UUIDModel trait added
* CamelCaseModel trait added
* Model class updated to use CamelCaseModel trait - deprecated, backwards-compatibility support only
* Eloquence now its own namespace (breaking change)
* EloquenceServiceProvider added use this if you want to overload the base model automatically (required for pivot model camel casing).

#### 1.0.2

* Relationships now support camelCasing for retrieval (thanks @linxgws)

#### 1.0.1

* Fixed an issue with dependency resolution

#### 1.0.0

* Initial implementation
* Camel casing of model attributes now available for both setters and getters

## License

The Laravel framework is open-sourced software licensed under the MIT license.

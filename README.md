# Eloquence

Eloquence is a package to extend Laravel 4's base Eloquent models and functionality.

It allows developers to continue using the PSR-0 standard when dealing with database field names in models. This package will be built on in the future to add more features to the great Eloquent library.

## Installation

Install the package via composer:

    composer require kirkbushell/eloquence ~1.1

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

## UUID migrations

When you install Eloquence, two things happen automatically (if you use the service provider):

1. It will re-bind the model class to Eloquence's model class (to allow for certain features to work) and
2. It will also re-bind the blueprint for schema migrations, allowing you to use UUID field migrations:

    Schema::create('some_table', function(Blueprint $table) {
        $table->uuid('name_of_column');
    });

Voila! You now have UUID column creation. This is simply a wrapper for doing the following:

    Schema::create('some_table', function(Blueprint $table) {
        $table->char('name_of_column', $length = 36);
    });

You're not done yet, though! Now just include the UUIDModel trait in your models:

    use Eloquence\Database\Traits\UUIDModel;

This will turn off id autoincrementing in your model, and instead automatically generate a UUID4 value for your id field. One benefit of this is that you can actually know the id of your record BEFORE it's saved!

## Note!

It should be noted that Eloquence DOES NOT CHANGE how you write your schema migrations. You should still be using snake_case when setting up your fields and tables in your database schema migrations. This is a good thing - snake_case of field names is the defacto standard within the Laravel community :)


## Changelog

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

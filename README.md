# Eloquence

![Version](https://img.shields.io/packagist/v/kirkbushell/eloquence.svg)
![Downloads](https://img.shields.io/packagist/dt/kirkbushell/eloquence.svg)
![Status](https://img.shields.io/travis/kirkbushell/eloquence/master.svg)

Eloquence is a package to extend Laravel's base Eloquent models and functionality.

It provides a number of utilities and classes to work with Eloquent in new and useful ways, 
such as camel cased attributes (for JSON apis), count caching, uuids and more.

## Installation

Install the package via composer:

    composer require kirkbushell/eloquence:~4.0

For Laravel 6, please install:

    composer require kirkbushell/eloquence:~3.0

For Laravel 5, please install the ~2.0 suite of releases.

    composer require kirkbushell/eloquence:~2.0

For Laravel 4, please install the 1.1.5 release. Please note that this is no longer supported.

    composer require kirkbushell/eloquence:1.1.5

## Usage

First, add the eloquence service provider to your config/app.php file:

    'Eloquence\EloquenceServiceProvider',

It's important to note that this will automatically re-bind the Model class
that Eloquent uses for many-to-many relationships. This is necessary because
when the Pivot model is instantiated, we need it to utilise the parent model's
information and traits that may be needed.

You should now be good to go with your models.

## Camel case all the things!

For those of us who prefer to work with a single coding standard right across our applications, 
using the CamelCaseModel trait will ensure that all those attributes, relationships and associated 
data from our Eloquent models persist through to our APIs in a camel-case manner. This is important 
if you are writing front-end applications, which are also using camelCase. This allows for a 
better standard across our application. To use:

    use \Eloquence\Behaviours\CamelCasing;

Put the above line in your models and that's it.

### Note!

Eloquence DOES NOT CHANGE how you write your schema migrations. You should still be using snake_case 
when setting up your fields and tables in your database schema migrations. This is a good thing - 
snake_case of field names is the defacto standard within the Laravel community :)


## UUIDs

Eloquence comes bundled with UUID capabilities that you can use in your models.

Simply include the Uuid trait:

    use Eloquence\Behaviours\Uuid;

And then disable auto incrementing ids:

    public $incrementing = false;

This will turn off id auto-incrementing in your model, and instead automatically generate a UUID4 value for your id field. One 
benefit of this is that you can actually know the id of your record BEFORE it's saved!

You must ensure that your id column is setup to handle UUID values. This can be done by creating a migration with the following 
properties:

    $table->char('id', $length = 36)->index();

It's important to note that you should do your research before using UUID functionality and whether it works for you. UUID 
field searches are much slower than indexed integer fields (such as autoincrement id fields).


### Custom UUIDs

Should you need a custom UUID solution (aka, maybe you don't want to use a UUID4 id), you can simply define the value you wish on 
the id field. The UUID model trait will not set the id if it has already been defined. In this use-case however, it's probably no good
to use the Uuid trait, as it's practically useless in this scenario.

## Behaviours

Eloquence comes with a system for setting up behaviours, which are really just small libraries that you can use with your Eloquent models.
The first of these is the count cache.

### Count cache

Count caching is where you cache the result of a count of a related table's records. A simple example of this is where you have a user who
has many posts. In this example, you may want to count the number of posts a user has regularly - and perhaps even order by this. In SQL,
ordering by a counted field is slow and unable to be indexed. You can get around this by caching the count of the posts the user
has created on the user's record.

To get this working, you need to do two steps:

1. Use the Countable trait on the model and 
2. Configure the count cache settings

#### Configure the count cache

To setup the count cache configuration, we need to have the model use Countable trait, like so:

```php
class Post extends Eloquent {
    use Countable;
    
    public function countCaches() {
        return [User::class];
    }
}
```

This tells the count cache that the Post model has a count cache on the User model. So, whenever a post is added, or modified or
deleted, the count cache behaviour will update the appropriate user's count cache for their posts. In this case, it would update `post_count`
on the user model.

The example above uses the following standard conventions:

* `post_count` is a defined field on the User model table
* `user_id` is the field representing the foreign key on the post model
* `id` is the primary key on the user model table

These are, however, configurable:

```php
class Post extends Eloquent {
    use Countable;
    
    public function countCaches() {
        return [
            'num_posts' => ['User', 'users_id', 'id']
        ];
    }
}
```

This example customises the count cache field, and the related foreign key, with `num_posts` and `users_id`, respectively.

Alternatively, you can be very explicit about the configuration (useful if you are using count caching on several tables
and use the same column name on each of them):

```php
class Post extends {
    use Countable;
    
    public function countCaches() {
        return [
            [
                'model'      => 'User',
                'field'      => 'num_posts',
                'foreignKey' => 'users_id',
                'key'        => 'id'
            ]
        ];
    }
}
```

If using the explicit configuration, at a minimum you will need to define the "model" parameter.  The "countField", "foreignKey",
and "key" parameters will be calculated using the standard conventions mentioned above if they are omitted.

With this configuration now setup - you're ready to go!


### Sum cache

Sum caching is similar to count caching, except that instead of caching a _count_ of a related table's records, you cache a _sum_
of a particular field on the related table's records. A simple example of this is where you have an order that has many items.
Using sum caching, you can cache the sum of all the items' prices, and store that sum in the order table.

To get this working -- just like count caching -- you need to do two steps:

1. Utilise the Summable trait on the model and
2. Configure the model for any sum caches

#### Configure the sum cache

To setup the sum cache configuration, simply do the following:

```php
class Item extends Eloquent {
    use Summable;
    
    public function sumCaches() {
        return [Order::class];
    }
}
```

This tells the sum cache manager that the Item model has a sum cache on the Order model. So, whenever an item is added, modified, or
deleted, the sum cache behaviour will update the appropriate order's sum cache for their items. In this case, it would update `item_total`
on the Order model.

The example above uses the following conventions:

* `item_total` is a defined field on the Order model table
* `total` is a defined field on the Item model table (the column we are summing) 
* `order_id` is the field representing the foreign key on the item model
* `id` is the primary key on the order model table

These are, however, configurable:

```php
class Item extends Eloquent {
    use Summable;
    
    public function sumCaches() {
        return [
            'item_total' => ['Order', 'total', 'order_id', 'id']
        ];
    }
}
```
    
Or using the verbose syntax:

```php
class Item extends Eloquent {
    use Summable;
    
    public function sumCaches() {
        return [
            [
                'model'       => 'Order',
                'columnToSum' => 'total',
                'field'       => 'item_total'
                'foreignKey'  => 'order_id',
                'key'         => 'id'
            ]
        ];
    }
}
```

Both of these examples implements the default settings.

With these settings configured, you will now see the related model's sum cache updated every time an item is added, updated, or removed.

### Sluggable models

Sluggable is another behaviour that allows for the easy addition of model slugs. To use, implement the Sluggable trait:

```php
class User extends Eloquent {
    use Sluggable;

    public function slugStrategy() {
        return 'username';
    }
}
```

In the example above, a slug will be created based on the username field of the User model. There are two other
slugs that are supported however, as well:

* id and
* uuid

The only difference between the two above, is that if you're using UUIDs, the slug will be generated previous
to the save, based on the uuid field. With ids, which are generally auto-increase strategies - the slug has
to be generated after the record has been saved - which results in a secondary save call to the database.

That's it! Easy huh?

## Changelog

#### 8.0.0

* Boost in version number to match Laravel
* Support for Laravel 7.3+

* Fixes a bug that resulted with the new guarded attributes logic in eloquent

#### 4.0.1

* Fixes a bug that resulted with the new guarded attributes logic in eloquent

#### 4.0.0

* Laravel 7 support (thanks, @msiemens!)

#### 3.0.0

* Laravel 6 support
* Better slug creation and handling

#### 2.0.7

* Slug uniqueness check upon slug creation for id-based slugs.

#### 2.0.6

* Bug fix when restoring models that was resulting in incorrect count cache values.

#### 2.0.3

* Slugs now implement Jsonable, making them easier to handle in API responses
* New artisan command for rebuilding caches (beta, use at own risk)

#### 2.0.2

* Updated PHP dependency to 5.6+
* CountCache and SumCache behaviours now supported via a service layer

#### 2.0.0

* Sum cache model behaviour added
* Booting of behaviours now done via Laravel trait booting
* Simplification of all behaviours and their uses
* Updated readme/configuration guide

#### 1.4.0

* Slugs when retrieved from a model now return Slug value objects.

#### 1.3.4

* More random, less predictable slugs for id strategies

#### 1.3.3

* Fixed a bug with relationships not being accessible via model properties

#### 1.3.2

* Slugged behaviour
* Fix for fillable attributes

#### 1.3.1

* Relationship fixes
* Fillable attributes bug fix
* Count cache update for changing relationships fix
* Small update for implementing count cache observer

#### 1.3.0

* Count cache model behaviour added
* Many-many relationship casing fix
* Fixed an issue when using ::create

#### 1.2.0

* Laravel 5 support
* Readme updates

#### 1.1.5

* UUID model trait now supports custom UUIDs (instead of only generating them for you)

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

# Eloquence

![Version](https://img.shields.io/packagist/v/kirkbushell/eloquence.svg)
![Downloads](https://img.shields.io/packagist/dt/kirkbushell/eloquence.svg)
[![Test](https://github.com/kirkbushell/eloquence/actions/workflows/test.yml/badge.svg)](https://github.com/kirkbushell/eloquence/actions/workflows/test.yml)

Eloquence is a package to extend Laravel's base Eloquent models and functionality.

It provides a number of utilities and attributes to work with Eloquent in new and useful ways,
such as camel cased attributes (such as for JSON apis and code style cohesion), data aggregation and more.

## Installation

Install the package via composer:

    composer require kirkbushell/eloquence

## Usage

Eloquence is automatically discoverable by Laravel, and shouldn't require any further steps. For those on earlier 
versions of Laravel, you can add the package as per normal in your config/app.php file:

    'Eloquence\EloquenceServiceProvider',

The service provider doesn't do much, other than enable the query log, if configured.

## Camel case all the things!

For those of us who prefer to work with a single coding style right across our applications, using the CamelCased trait 
will ensure you can do exactly that. It transforms all attribute access from camelCase to snake_case in real-time,
providing a unified coding style across your application. This means everything from attribute access to JSON API 
responses will all be camelCased. To use, simply add the CamelCased trait to your model:

    use \Eloquence\Behaviours\HasCamelCasing;

### Note!

Eloquence ***DOES NOT CHANGE*** how you write your schema migrations. You should still be using snake_case when setting 
up your columns and tables in your database schema migrations. This is a good thing - snake_case of columns names is the 
defacto standard within the Laravel community and is widely-used across database schemas, as well.

## Behaviours

Eloquence comes with a system for setting up behaviours, which are really just small libraries that you can use with your 
Eloquent models. The first of these is the count cache.

### Count cache

Count caching is where you cache the result of a count on a related model's record. A simple example of this is where you 
have posts that belong to authors. In this situation, you may want to count the number of posts an author has regularly,
and perhaps even order by this count. In SQL, ordering by an aggregated value is unable to be indexed and therefore - slow.
You can get around this by caching the count of the posts the author has created on the author's model record.

To get this working, you need to do two steps:

1. Use the HasCounts trait on the child model (in this, case Post) and
2. Configure the count cache settings by using the CountedBy attribute.

#### Configuring a count cache

To setup a count cache configuration, we add the HasCounts trait, and setup the CountedBy attribute:

```php
use Eloquence\Behaviours\CountCache\CountedBy;
use Eloquence\Behaviours\CountCache\HasCounts;
use Illuminate\Database\Eloquent\Model;

class Post extends Model {
    use HasCounts;

    #[CountedBy]
    public function author(): BelongsTo
    {
        return $this->belongsTo(Author::class);
    }
}
```

This tells the count cache behaviour that the model has an aggregate count cache on the Author model. So, whenever a post 
is added, modified or deleted, the count cache behaviour will update the appropriate author's count cache for their 
posts. In this case, it would update `post_count` field on the author model.

The example above uses the following standard conventions:

* `post_count` is a defined field on the User model table

It uses your own relationship to find the related record, so no other configuration is required!

Of course, if you have a different setup, or different field names, you can alter the count cache behaviour by defining
the appropriate field to update:

```php
class Post extends Model {
    use HasCounts;

    #[CountedBy(as: 'total_posts')]
    public function author(): BelongsTo
    {
        return $this->belongsTo(Author::class);
    }
}
```

When setting the as: value (using named parameters here from PHP 8.0 for illustrative and readability purposes), you're 
telling the count cache that the aggregate field on the Author model is actually called `total_posts`.

HasCounts is not limited to just one count cache configuration. You can define as many as you need for each BelongsTo
relationship, like so:

```php
#[CountedBy(as: 'total_posts')]
public function author(): BelongsTo
{
    return $this->belongsTo(Author::class);
}

#[CountedBy(as: 'num_posts')]
public function category(): BelongsTo
{
    return $this->belongsTo(Category::class);
}
```

### Sum cache

Sum caching is similar to count caching, except that instead of caching a _count_ of the related model objects, you cache a _sum_
of a particular field on the child model's object. A simple example of this is where you have an order that has many items.
Using sum caching, you can cache the sum of all the items' prices, and store that as a cached sum on the Order model.

To get this working -- just like count caching -- you need to do two steps:

1. Add the HasSums to your child model and
2. Add SummedBy attribute to each relationship method that requires it.

#### Configure the sum cache

To setup the sum cache configuration, simply do the following:

```php
use Eloquence\Behaviours\SumCache\HasSums;
use Eloquence\Behaviours\SumCache\SummedBy;
use Illuminate\Database\Eloquent\Model;

class Item extends Model {
    use HasSums;

    #[SummedBy(from: 'amount', as: 'total_amount')]
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
```

Unlike the count cache which can assume sensible defaults, the sum cache needs a bit more guidance. The example above 
tells the sum cache that there is an `amount` field on Item that needs to be summed to the `total_amount` field on Order.

### Cache recommendations

Because the cache system works directly with other model objects and requires multiple writes to the database, it is
strongly recommended that you wrap your model saves that utilise caches in a transaction. In databases like Postgres,
this is automatic, but for databases like MySQL you need to make sure you're using a transactional database engine
like InnoDB.

The reason for needing transactions is that if any one of your queries fail, your caches will end up out of sync. It's 
better for the entire operation to fail, than to have this happen. Below is an example of using a database transaction
using Laravel's DB facade:

```php
DB::transaction(function() {
    $post = new Post;
    $post->authorId = $author->id;
    $post->save();
});
```

If we return to the example above with posts having authors - if this save was not wrapped in a transaction, and the post
was created but for some reason the database failed immediately after, you would never see the count cache update in the
parent Author model, you'll end up with erroneous data that can be quite difficult to debug.

### Sluggable

Sluggable is another behaviour that allows for the easy addition of model slugs. To use, implement the Sluggable trait:

```php
class User extends Model {
    use HasSlugs;

    public function slugStrategy(): string
    {
        return 'username';
    }
}
```

In the example above, a slug will be created based on the username field of the User model. There are two other
slugs that are supported, as well:

* id and
* uuid

The only difference between the two above, is that if you're using UUIDs, the slug will be generated prior to the model
being saved, based on the uuid field. With ids, which are generally auto-increase strategies - the slug has to be 
generated after the record has been saved - which results in a secondary save call to the database.

That's it! Easy huh?

# Upgrading from v10
Version 11 of Eloquence is a complete rebuild and departure from the original codebase, utilising instead PHP 8.1 attributes
and moving away from traits/class extensions where possible. This means that in some projects many updates will need to 
be made to ensure that your use of Eloquence continues to work.

## 1. Class renames

* Camelcasing has been renamed to HasCamelCasing
* Sluggable renamed to HasSlugs

## 2. Updates to how caches work
All your cache implementations will need to be modified following the guide above. But in short, you'll need to import
and apply the provided attributes to the relationship methods on your models that require aggregated cache values.

The best part about the new architecture with Eloquence, is that you can define your relationships however you want! If 
you have custom where clauses or other conditions that restrict the relationship, Eloquence will respect that. This makes
Eloquence now considerably more powerful and supportive of individual domain requirements than ever before.

Let's use a real case. This is the old approach, using Countable as an example:

```php
class Post extends Model
{
    use Countable;
    
    public function countCaches() {
        return [
            'num_posts' => ['User', 'users_id', 'id']
        ];
    }
}
```

To migrate that to v11, we would do the following:

```php
use Eloquence\Behaviours\CountCache\CountedBy;

class Post extends Model
{
    use \Eloquence\Behaviours\CountCache\HasCounts;
    
    #[CountedBy(as: 'num_posts')]
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
```

Note the distinct lack of required configuration. The same applies to the sum behaviour - simply migrate your configuration 
away from the cache functions, and into the attributes above the relationships you wish to have an aggregated cache 
value for.

## Changelog

#### 11.0.4

* Bug fix provided by #120 addressing the creation of new models without related model objects

#### 11.0.3

* Bug fix for count cache when relation is removed (#118)
* Identified and applied a similar bugfix for the sum cache

#### 11.0.2

* Fixed a bug where relationships were not being returned

#### 11.0.1

* Fixed dependency error to support Laravel 11

#### 11.0.0

* Complete rework of the Eloquent library - version 11 is **_not_** backwards-compatible
* UUID support removed - both UUIDs and ULIDs are now natively supported in Laravel and have been for some time
* Cache system now works directly with models and their relationships, allowing for fine-grained control over the models it works with
* Console commands removed - model caches can be rebuilt using Model::rebuildCache() if something goes awry
* Fixed a number of bugs across both count and sum caches
* CamelCasing renamed to CamelCased
* Syntax, styling, and standards all modernised

#### 10.0.0

* Boost in version number to match Laravel
* Support for Laravel 10.0+
* Replace date casting with standard Laravel casting (https://laravel.com/docs/10.x/upgrade#model-dates-property)

#### 9.0.0

* Boost in version number to match Laravel
* Support for Laravel 9.0+
* Updated to require PHP 8.1+
* Resolved method deprecation warnings

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
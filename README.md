# InkSphinxize

Sphinx Search for Laravel 4 - InkSphinxize

## Installation

First, you'll need to add the package to the `require` attribute of your `composer.json` file:

```json
{
    "require": {
        "ink/ink-sphinxize": "dev-master"
    },
}
```

Afterwards, run `composer update` from your command line.

Then, add `'Ink\InkSphinxize\InkSphinxizeServiceProvider',` to the list of service providers in `app/config/app.php`
and add `'InkSphinxizeSearch' => 'Ink\InkSphinxize\Facades\InkSphinxize',` to the list of class aliases in `app/config/app.php`.

From the command line again, run `php artisan config:publish ink/ink-sphinxize`.


## Updating your Models

Define a private static property `$sphinxize` with the definitions:

```php

class Post extends Eloquent
{
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'posts';
        	
	/**
	 * InkSphinxize configs
	 *
	 * @var array
	 */
	private static $sphinxize = [
		'index'   => 'posts',
		'pk'      => 'id',
		'perPage' => 15,
	];

}
```

That's it!

## Examples

```phpe

Route::get('/', function()
{
	$perPage = 5; // Override $sphinxize perPage value
	
	$ids = Post::ss('Azerbaijan')
			->ids()
			->skip(5)
			->take($perPage)
			->get();
	// Returns [2, 34, 66, 98, 103, ...]
	
	$records = Post::ss('Azerbaijan')
				->limit(0, $perPage)
				->get();
	// Returns \Illuminate\Database\Eloquent\Collection
	
	$records = Post::ss('Azerbaijan')
				->limit(0, $perPage)
				->with('category')
				->get();
	// Returns \Illuminate\Database\Eloquent\Collection with Eager loading
	
	$records = Post::ss('Azerbaijan')
				->with(array('category', 'tags' => function($query){
					$query->where('name', 'like', '%baku%');
				}))
				->paginate($perPage);
	// Returns \Illuminate\Database\Eloquent\Collection with Eager loading
	
	$paginator = Paginator::make($records->toArray(), count($records->toArray()), $perPage);
	// Paginator
				
	dd($records);
});

```

## Bugs and Suggestions

Please use Github for bugs, comments, suggestions. Pull requests are preferred!


## Copyright and License

InkSphinxize was written by Orkhan Maharramli and released under the MIT License. See the LICENSE file for details.

Copyright 2013 Orkhan Maharramli

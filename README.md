##Username wrangler — A Laravel Username Generator and Suggester

### 1. Generator
Easily generate, and suggest unique usernames for any given Laravel User Model

1. [Most Recent Update](#most-recent-update)
2. [Install](#install)
3. [Set Up](#set-up)
4. [Config](#config)
      - [Allowed Characters](#allowed-characters)
5. [Basic Usage](#basic-usage)
    - [generate($name)](#generatename)
    - [generateFor($model)](#generateformodel)
    - [GeneratesUsernames Trait](#generatesusernames-trait)
    - [UsernameWrangler Facade](#usernameWrangler-facade)
6. [Other Examples](#other-examples)
    - [With a Separator](#with-a-separator)
    - [Upper Case](#upper-case)
    - [Additional Casing Options](#additional-casing-options)
    - [Mixed Case](#mixed-case)
    - [Minimum Length](#minimum-length)
    - [Maximum Length](#maximum-length)
    - [Other Character Sets](#other-character-sets)
7. [Drivers](#drivers)
    - [Extending](#extending)
8. [License](#license)
9. [Change Log](#change-log)


## Install
Via Composer

```bash
$ composer require simtabi/username-wrangler
```

### Publish Config

This will add the config to `config/username-wrangler.php`


```bash
$ php artisan vendor:publish --provider="Simtabi\UsernameWrangler\ServiceProvider"
```

## Quickstart 

This section will help you get up and running fast.

The following steps will be the same for all Laravel versions and assumes you're adding the package to a new installation.

**User Model**

In `App\Models\User` (or `App\User` for Laravel 7) add the `WithUsernameWrangler` trait. 
Add `'username'` to the fillable property.

```php

// ...
use Simtabi\UsernameWrangler\Traits\WithUsernameWrangler;

class User extends Authenticatable
{
	// ...
	use WithUsernameWrangler;
	
	protected $fillable = [
		// ...
		'username',
	];
	
	// ...

}
```

**Database Migration**

In your `database/2014_10_12_000000_create_users_table` add a username column.

```php
class CreateUsersTable extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            // ...
            $table->string('username')->unique();
            // ...
        });
    }
}
```

## Config

**This is in the process of being updated on the wiki**

See the [default config](https://github.com/simtabi/username-wrangler/blob/master/src/config/username-wrangler.php)

By default, the `Generator` class has the following configuration:

| Config | Value | Type |
|:------:|:-----:|:----:|
| Unique Username | `true` | boolean |
| Separator | `''` | string (should be single character) |
| Case | `'lower'` | string (one of lower, upper, or mixed) |
| Username DB Column | `'username'` | string |

The config is stored in `config/username-wrangler.php`

You can override config on a new instance by `new Generator([ 'unique' => false ]);` etc.

### Allowed Characters

If you need to include additional characters beyond just `'A-Za-z'` you'll need to update the `allowed_characters` config option.

You should also update `'convert_to_ascii'` to `false` if you want the result to be in the same set.

For example

```
   'allowed_characters' => 'А-Яа-яA-Za-z',   // Would also allow Cyrillic characters
   
   'allowed_characters' => 'А-Яа-яA-Za-z-_' // Includes Cyrillic, Latin characters as well as '-' and '_'
   
   'allowed_characters' => '\p{Cryillic}\p{Greek}\p{Latin}\s ' // Includes cryillic, greek and latin sets and all spaces
```

Please note that all characters not included in this list are removed before performing any operations. 
If you get an empty string returned double check that the characters used are included. 

## Basic Usage

#### generate($name)
Create a new instance and call `generate($name)`

```php
use Simtabi\UsernameWrangler\UsernameWrangler;

$generator = (new UsernameWrangler())->generator();
$username  = $generator->generate('Test User');

```

Returns

```php
'testuser'
```

If you do not provide a name to the generate method an adjective and noun will be chosen as the name at random, using noun and adjective word lists from [alenoir/username-generator](https://github.com/alenoir/username-generator), which will then be converted to a username.

```php
use Simtabi\UsernameWrangler\Facades\UsernameWranglerFacade;

$username = UsernameWranglerFacade::generator()->generate();
```

Returns something similar to

```php
'monogamousswish'
```


#### generateFor($model)
Create a new instance and call `generateFor($model)`

This will access the model's `name` property and convert it to a username.

```php
use Simtabi\UsernameWrangler\UsernameWrangler;

class User
{
	public $name = 'Some Other User';
	
	public function getUsername()
	{
		$generator = (new UsernameWrangler())->generator();
		return $generator->generateFor($this);
	}
}

```

Returns

```php

'someotheruser'

```


## GeneratesUsernames Trait

This package also comes with a `GeneratesUsernames` trait that you can add to your model and it will automatically call the username generator when the model is saving without the specified username column.

*Note: you will also need to include the `FindSimilarUsernames` trait either way*

```php
use Simtabi\UsernameWrangler\Traits\WithUsernameWrangler;

class User 
{
	use WithUsernameWrangler;
}

```

You can also add custom config to call before the username is generated.

Override the `generatorConfig` method in your model

```php
use Simtabi\UsernameWrangler\Traits\WithUsernameWrangler;

class User 
{
	use WithUsernameWrangler;
	
	public function generatorConfig(&$generator) 
	{
		$generator->setConfig([ 'separator' => '_' ]);
	}
}

```

If you need to modify the data before handing it off to the generator, override the `getField` method on your model. 
For example if you have a first and last name rather than a single name field, you'll need to add this to your model.

```php
class User 
{
	// ...
	
	public function getField(): string
	{	
		return $this->first_name . ' ' . $this->last_name;
	}
	
	// ...
}
```

*Note: if your code still uses a custom `getName`, it will still work, however it was replaced with `getField` in v2.1 when driver support was added.*

## UsernameWrangler Facade

This package includes a `UsernameWrangler` facade for easy access

```php
UsernameWrangler::generator()->generate('Test User');

UsernameWrangler::generator()->generateFor($user);

UsernameWrangler::generator()->setConfig([ 'separator' => '_' ])->generate('Test User');
```

## Other Examples

### With a Separator

```php
use Simtabi\UsernameWrangler\UsernameWrangler;

$generator = (new UsernameWrangler())->generator([ 'separator' => '_' ]);
$generator->generate('Some User');

```

Returns 

```
some_user
```

### Upper Case

```php
use Simtabi\UsernameWrangler\UsernameWrangler;

$generator = (new UsernameWrangler())->generator([ 'case' => 'upper' ]);
$generator->generate('Some User');

```

Returns 

```
SOMEUSER
```

### Additional Casing Options

To change the casing, we make use of the [Laravel String Helpers](https://laravel.com/docs/master/helpers#strings-method-list) so any value that changes the case will work.

**Studly (Pascal)**

```php
UsernameWrangler::generator()->setConfig([ 'case' => 'studly' ])->generate('test user');
// Returns 'TestUser'
```

When using studly case the laravel helper will remove the spaces between separate words so if a separator is used it will be overridden. 
You would need to use title case (seen below) in order to have the same effect.

```php
UsernameWrangler::generator()->setConfig([ 'case' => 'studly', 'separator' => '_' ])->generate('test user');
// Returns 'TestUser'
```

**Title**

This is the same as studly but the laravel helper will not remove spaces, so it can be used in conjunction with a separator

```php
UsernameWrangler::generator()->setConfig([ 'case' => 'title' ])->generate('test user');
// Returns 'TestUser'

UsernameWrangler::generator()->setConfig([ 'case' => 'title', 'separator' => '_' ])->generate('test user');
// Returns 'Test_User'
```

**Ucfirst**


```php
UsernameWrangler::generator()->setConfig([ 'case' => 'ucfirst' ])->generate('test user');
// Returns 'Testuser'
```

### Mixed Case

```php
use Simtabi\UsernameWrangler\UsernameWrangler;

$generator = (new UsernameWrangler())->generator([ 'case' => 'mixed' ]);
$generator->generate('Some User');

```

Returns 

```
SomeUser
```

---

Note: Mixed case will just ignore changing case altogether

```php
use Simtabi\UsernameWrangler\UsernameWrangler;

$generator = (new UsernameWrangler())->generator([ 'case' => 'mixed' ]);
$generator->generate('SoMe WeIrD CapitaliZation');

```

Returns 

```
SoMeWeIrDCapitaliZation
```

*Note: if you pass an invalid value for the `case` option, mixed case will be used.*

### Minimum Length

If you want to enforce a minimum length for usernames generated change the `min_length` option in `config/username-wrangler.php` 

```php
'min_length' => 6,
```

By default, if the generator generates a username less than the minimum length it will pad the end of it with a random digit between 0 and 9.

For example

```php

UsernameWrangler::generator()->generate('test');

// Would return the following where 0 is a random digit

'test00' 

```

**Alternatively you can throw an exception when the minimum length has not been reached**

In `config/username-wrangler.php` set

```php
'throw_exception_on_too_short' => true,
```

```php
UsernameWrangler::generator()->generate('test');
```

Would throw a `UsernameTooShortException`

### Maximum Length

If you want to enforce a maximum length for usernames generated change the `max_length` option in `config/username-wrangler.php` 

```php
'max_length' => 6,
```

By default if the generator generates a username more than the minimum length it will cut it to the max length value and then try to make it unique again. 
If that becomes too long it will remove one character at a time until a unique username with the correct length has been generated.

For example

```php

UsernameWrangler::generator()->generate('test user');

'testus' 

```

**Alternatively you can throw an exception when the maximum length has been exceeded**

In `config/username-wrangler.php` set

```php
'throw_exception_on_too_long' => true,
```

```php
UsernameWrangler::generator()->generate('test user');
```

Would throw a `UsernameTooLongException`

### Other Character Sets

Any other character set can be used if it's encoded with UTF-8. You can either include by adding the set to the `'allowed_characters'` option.

Alternatively you can set `'validate_characters'` to `false` to not check.

**You will need to set `'convert_to_ascii'` to `false` either way**

```php
use Simtabi\UsernameWrangler\UsernameWrangler;

$generator = (new UsernameWrangler())->generator([
    'allowed_characters' => '\p{Greek}\p{Latin}\s ',
    'convert_to_ascii'   => false,
]);

$generator->generate('Αυτό είναι ένα τεστ');

// Returns

'αυτόείναιένατεστ'
```

## Drivers

2 drivers are included, `NameDriver` (default) and `EmailDriver`

To use a specific driver, if none is specified the default is used.

```php
UsernameWrangler::generator()->usingEmail()->generate('testuser@example.com');

// Returns

'testuser'
```
OR
```php
use Simtabi\UsernameWrangler\UsernameWrangler;

$generator = (new UsernameWrangler())->generator();
$generator->setDriver('email');
$generator->generate('test.user77@example.com');

// Returns

'testuser'
```

### Extending

You can make your own custom drivers that extend `Simtabi\UsernameWrangler\Drivers\BaseDriver` or override an existing one.

Custom drivers require a `public $field` property to be set which is the name of the field on the model to use to generate the username.

Drivers will perform the following operations in order:

```php
[
	'stripUnwantedCharacters',     // Removes all unwanted characters from the text
	'convertCase',                 // Converts the case of the field to the set value (upper, lower, mixed)
	'collapseWhitespace',          // Collapses any whitespace to a single space
	'addSeparator',                // Converts all spaces to separator
	'makeUnique',                  // Makes the username unique (if set)
]
``` 

In your custom driver you can add a method to perform an operation before or after any of the above operations. 

```php
public function beforeConvertCase(string $text): string 
{

	// --
	
}

public function afterStripUnwantedCharacters(string $text): string 
{

	// --
	
}
```

Additionally, if there is any operation you want to do as the very first or last thing you can use the first and last hooks.

```php
public function first(string $text): string 
{
    // Happens first before doing anything else
}

public function last(string $text): string 
{
    // Happens last just before returning
}
```

#### Example

For example if you wanted to append `-auto` to all automatically generated usernames, you could make a new driver in `App\Drivers\AppendDriver`

```php
namespace App\Drivers;

use Simtabi\UsernameWrangler\Drivers\Suggester\BaseSuggesterDriver;

class AppendDriver extends BaseSuggesterDriver
{	
    public $field = 'name';
    
    public function afterMakeUnique(string $text): string
    {
    	return $text . '-auto';
    }
}
```

And then in `config/username-wrangler.php` add the driver to the top of the drivers array to use it as default.

```php
'drivers' => [
	'append' => \App\Drivers\AppendDriver::class,
        ...
    ],
```
### 2. Suggester

The suggester would be useful if you want to show your users a list of suggested usernames based on their entry if their selection is unavailable.


## Defaults

The suggester will generate `3` unique usernames based on the given name. It will use the `increment` driver which will use the
`Simtabi\UsernameWrangler\Factories\Generator` class to convert the name to a username and then add incrementing numbers on the end to make them unique.

If no name is entered it will generate random usernames, same as `Simtabi\UsernameWrangler\Factories\Generator`

## Usage

### Available Methods

#### suggest

The `suggest()` method accepts an optional parameter of the name to suggest usernames for.

```php
use Simtabi\UsernameWrangler\UsernameWrangler;

$suggester = (new UsernameWrangler())->suggester();

$suggester->suggest(); // Returns a collection of random unique usernames

$suggester->suggest('test user'); // Returns a collection of unique usernames based on the name 'test user'
```

#### setDriver

This will allow you to set a different driver than the default.

```php
$suggester->setDriver('random');
```

This method returns an instance of the `Suggester` class, so you're able to chain methods.

#### setAmount

This will allow you to set a different amount of suggestions than the default.

```php
$suggester->setAmount(10);
```

This method returns an instance of the `Suggester` class, so you're able to chain methods.

#### setGeneratorConfig

This will allow you to override the `TaylorNetwork\UsernameGenerator\Generator` config.

```php
$suggester->setGeneratorConfig([
    'separator' => '*',
]);
```

Same as `setAmount` and `setDriver` this method also returns the `Suggester` instance.

### Example

#### Basic with Random Usernames

```php
$suggester->suggest();
```

#### Basic with Entered Name

```php
$suggester->suggest('test user');
```

#### Different Driver and Amount

```php
$suggester->setDriver('random')->setAmount(5)->suggest('test user');
```

This will use the `Random` driver to append random numbers after the username and generate 5 usernames.

#### Using the Facade

A `UsernameWrangler` facade is included so all the methods can be accessed that way.

```php
use Simtabi\UsernameWrangler\UsernameWrangler;

UsernameWrangler::suggester()->suggest();
```

## Custom Drivers

You can create any custom drivers by extending the `Simtabi\UsernameWrangler\Drivers\Generator\BaseDriver` class.

```php
namespace App\SuggesterDrivers;

use Simtabi\UsernameWrangler\Drivers\Generator\BaseDriver;

class CustomDriver extends BaseDriver
{
    public function makeUnique(string $username): string
    {
        return $username;
    }
}
```

The only requirement is that you implement the `makeUnique` method to make the username unique in some way.

You'll also need to register the driver in `config/username_wrangler.php`

```php
'suggester'  => [
   // ...
   'drivers' => [
       'custom' => CustomDriver::class,
       // ...
   ],
],
```

You can access it using the key you set.

```php
UsernameWrangler::suggester()->setDriver('custom')->suggest();
```

Check out the [CHANGELOG](CHANGELOG.md) in this repository for all the recent changes.

## Credits

- [taylornetwork/username-suggester](https://github.com/taylornetwork/username-suggester) - Code and Idea.
- [taylornetwork/laravel-username-generator](https://github.com/taylornetwork/laravel-username-generator) - Code and Idea.

## License

UsernameWrangler is open-sourced software licensed under [the MIT license](LICENSE.md).

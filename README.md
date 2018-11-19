# DORM - Doug's ORM

## Example Usage

### Database connection

First and foremost, connect to the database like this:

```php
Database::connect([
	'host' => 'yourhost',
	'user' => 'yourusername',
	'password' => 'yourpassword',
	'dbname' => 'yourdatabase'
]);
```

Now you are ready to use DORM's functionality.

### Using the Model class

Assume you have a table called users with an id, a name and an email field.

Extend the Model class and set $table, $fillable, and $defaults as required. The constructor must take no parameters. The accessors and mutators in this example aren't necessary.

```php
use Dorm\Model;

class User extends Model {
	protected static $table = 'users';
	protected static $fillable = ['name', 'email'];
	protected static $defaults = [
		'name' => "No Name",
		'email' => "nomail@example.com"
	];
}
```

Now you can use the User class like this:

```php
$user = User::find(1);	#	retrieve the user with id 1
$user->setName("Doug");
$user->save();

# create automatically saves the new model and assigns it an id
$user2 = User::create([
	'name' => "Bob",
	'email' => "bob@example.com"
)];

# new makes a new instance without saving (id is 0)
$user3 = User::new([
	'name' => "Billy",
	'email' => "billy@example.com"
)];

# user3 record is saved and its id is updated
user3->save();

```

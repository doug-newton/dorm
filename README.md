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

If you have a table created like this:

``sql
create table users (
	id int not null auto_increment primary key,
	name varchar(255) not null,
	email varchar(255) not null
);
```

Extend the Model class and set $table, $fillable, and $defaults as required. Additionally, implement input($data) and output() - which process and create arrays of object data respectively. The constructor must take no parameters. The accessors and mutators in this example aren't necessary.

```php
use Dorm\Model;

class User extends Model {
	protected static $table = 'users';
	protected static $fillable = ['name', 'email'];
	protected static $defaults = [
		'name' => "No Name",
		'email' => "nomail@example.com"
	];

	private $name;
	private $email;

	public function __construct() {
	}

	protected function input($data) {
		$this->name = $data['name'];
		$this->email = $data['email'];
	}

	protected function output() {
		return [
			'name' => $this->name,
			'email' => $this->email
		];
	}

	#	accessors 

	public function getName() {
		return $this->name;
	}

	public function getEmail() {
		return $this->email;
	}

	#	mutators

	public function setName($name) {
		$this->name = $name;
	}

	public function setEmail($email) {
		$this->email = $email;
	}
}
```

Now you can use the User class like this:

```php
$user = User::find(1);	#	retrieve the user with id 1
$user->setName("Doug");
$user->save();

$user2 = User::create([
	'name' => "Bob",
	'email' => "bob@example.com"
)];
```

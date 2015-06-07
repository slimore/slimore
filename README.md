# Slimore

The fully (H)MVC framework based on the [Slim PHP Framwork v2.6.x](https://github.com/slimphp/Slim) .

### Install

> Require php >=5.4.0 .

composer.json :

```json
{
	"require" : {
		"slimore/slimore" : "*"
	}
}
```

Install :

	$ composer install

### Directory structure

Single module :

	/
		app/
			controllers/
			models/
			views/
		configs/
			routes.php
			settigns.php
		public/
			.htaccess
			index.php
		vendor/
			...
		composer.json

Multi modules :

	/
		app/
			frontend/
				controllers/
				models/
				views/
			backend/
				controllers/
				models/
				views/
			api/
				controllers/
				models/
				views/
			...
		configs/
			routes.php
			settings.php
		public/
			.htaccess
			index.php
		vendor/
			...
		composer.json

### Usige

.htaccess :

```htaccess
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^ index.php [QSA,L]
```

index.php :

```php
<?php

define('APP_PATH',  realpath(__DIR__ . '/../app') . DIRECTORY_SEPARATOR);
define('BASE_URL',  str_replace('index.php', '', $_SERVER['PHP_SELF']));

require __DIR__ . '/../vendor/autoload.php';

$app = new \Slimore\Mvc\Application([
    'debug'           => true,
	'path'            => APP_PATH,
	'baseURL'         => BASE_URL,
	//'modules'       => ['home', 'admin', 'api'], // Multi-Modules
	//'defaultModule' => 'home',
    'autoloads'     => [
        APP_PATH . 'path/xx/xx'
    ]
]);

//$app->dbConnection(); // if using database

$app->get("/", function() {
    echo "Hello world!";
});

// Routes

//$app->get('/news', 'NewsController:read');
//$app->get('/news', 'Home\Controllers\NewsController:read');
//$app->post('/news', 'Home\Controllers\NewsController:create');
//$app->put('/news/:id', 'Home\Controllers\NewsController:update');
//$app->delete('/news/:id', 'Home\Controllers\NewsController:delete');

// Auto routes => /:action, /:controller/:action, /:module/:controller/:action
$app->autoRoute();

$app->run();
```

Model :

```php
<?php

class Article extends \Slimore\Mvc\Model
{
	//protected  $table      = 'you_table_name';
	//protected  $primaryKey = 'your_id';
}
```

> Using Eloquent ORM / Model : [http://laravel.com/docs/5.0/eloquent](http://laravel.com/docs/5.0/eloquent)

View :

	<?php
	// using php statement
	?>
	<title><?=$title?></title>
	<?=$var?>
	<?php if ($exp) : ?>
	<?php else : ?>
	<?php endif; ?>

Controller :

```
<?php

// Multi-module
//namesapce Frontend\Controllers;

use \Slimore\Database\Manager as DB;

class IndexController extends \Slimore\Mvc\Controller
{
    public function index()
    {
		// Using model, same Laravel
		$article = Article::find(1);

		// query builder
        $news = $this->db->table('news')
						 ->select(['nid', 'cid', 'title', 'content', 'add_time'])
						 ->where(['cid' => 0])
						 ->orderBy('nid', 'DESC')
						 ->get();
        //print_r($news);

		// Basic database usage, same Laravel
		$results = DB::select('select * from users where id = ?', [1]);

		// Slim application methods
		// request
		$get = $this->request->get();

		// response
		//$this->response->headers->set('Content-Type', 'application/json');

		// view
		$this->view->setData(array(
            'color' => 'red',
            'size' => 'medium'
        ));

		 // render views/index.php
		$this->render('index', [
			'title'   => 'Hello world!' . $article->title,
			'article' => $article
		]);

		// output json
		/*$this->json([
			'status' => 200,
			'message' => 'xxxxxx',
			'data' => $article
		]);*/
    }
}
```

> Using Slim : [http://docs.slimframework.com/](http://docs.slimframework.com/)

### Dependents

- [Slim framework](https://github.com/slimphp/Slim)
- [illuminate/database](https://github.com/illuminate/database)
- [illuminate/events](https://github.com/illuminate/events)

### Components

- Captcha
- Debug\Simpler
- Functions
- FileCache
- Http\Client
- Image\Gd
- Log\Writer
- Pagination
- Uploader
- ...

### Changes

[Change logs](https://github.com/slimore/slimore/blob/master/CHANGE.md)

### License

The [MIT License](https://github.com/slimore/slimore/blob/master/LICENSE).

Copyright (c) 2015 Pandao
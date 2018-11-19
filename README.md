# SRoute
SRoute - Fast, powerful routing provider to your applications based on PHP

##Installation
#### Via Composer

	composer require scythestudio/routing





## USAGE

"index.php":
```
require __DIR__.'/vendor/autoload.php';

use ScytheStudio\Routing\SRoute;
use ScytheStudio\Request;

//Your routes

SRoute::get("/test", function () {
	echo "Hello world";
})->save();

SRoute::get("/", "Controllers\LandingPageController@show")->middleware("Middlewares\TestMiddleware")->save();

Request::instance()->matchRoute();
```

Controller:
```
	namespace Controllers;
	
	public class LandingPageController {
	
		public function show() {
			echo "Hello World";
		}
	}
	
```

Middleware:

```php
	namespace Middlewares; 
	
	use ScytheStudio\Routing\Middleware;
	
	public class TestMiddleware implements Middleware {
		public function handle() {
			if(0 == 0) {
				return true;
			}
			
			return false;
		}
	}
```

Others:

```php
//Named

SRoute::get("/", function() {})->name("index")->save();

use ScytheStudio\Routing\RouteHelper;

echo RouteHelper::instance()->getRouteUrl("index"); 

//Arguments

SRoute::get("{ID}", function($ID) {
	echo $ID;
})->save();

//Regex

SRoute::get("{ID}", function($ID) {})->where(array("ID" => "/^[0-9]+$/"))->save();

//Request

use ScytheStudio\Routing\Request;

<input type="file" name="test_file">
<input type="text" name="string">

Request::instance()->file("test_file");
Request::instance()->input("string");

//Admission to old("input") *dont use

Request::instance()->old("string");


```

## TODO
- Route groups
- Route after
- Unlimited routes
- Request data from inputs
- Request "back" data
- Redirects
- Routing throttle
- Help functions
- Much more!




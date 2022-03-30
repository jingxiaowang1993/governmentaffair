<h1 align="center"> GovernmentAffair </h1>

<p align="center"> for Laravel</p>


## Installation

```shell
composer require golive_jxw/governmentaffair -vvv
```
## Configuration
The defaults configuration settings are set in config/governmentaffair.php. Copy this file to your own config directory to modify the values. You can publish the config using this command:
```shell
php artisan vendor:publish --provider="Government\Affair\ServiceProvider"
```
## Usage

SingleSignOn
```php
use Government\Affair\Classes\User;


$user = new User();
return $user->auth($goto);
```
TicketAuthentication
```php
return $user->ticket($ticket);
```
UserInfo
```php
return $user->info($token);
```
## License

MIT

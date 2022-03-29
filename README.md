<h1 align="center"> GovernmentAffair </h1>

<p align="center"> for Zhejiang</p>


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
$user = new \Government\Affair\Classes\User();
return $user->auth($goto);
```
TicketAuthentication
```php
$user = new \Government\Affair\Classes\User();
return $user->ticket($ticket);
```
UserInfo
```php
$user = new \Government\Affair\Classes\User();
return $user->info($token);
```
## License

MIT

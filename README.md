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

//alipay-支付宝入口
//ZhejiangOffice-同源APP入口（含浙里办APP及其他同源适配APP容器环境）

$user = new User();
$user->auth($goto,'ZhejiangOffice');
```
TicketAuthentication
```php
use Government\Affair\Classes\User;


$user = new User();
$user->ticket($ticket);
```
UserInfo
```php
use Government\Affair\Classes\User;


$user = new User();
$user->info($token);
```
## License

MIT

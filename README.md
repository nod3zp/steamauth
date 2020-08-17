Download Repository and unpack files (steamauth.php and openid.php)

Require and use lib

```php
require_once(__DIR__ . '/SteamAuth.php');

use nod3zp\SteamAuth;
```

Define **steamapi**
```php
$steam = new SteamAuth([
    'apikey' => 'XX...XX',
    'domain' => 'steam-test.loc',
    'logoutpage' => '/callback/logout',
    'loginpage' => '/callback/login'
]);
```

User status
```php
if($steam->isAuth())
{
    var_dump($_SESSION['steamuser']);
} else {
    $steam->login();
}
```

Login
```php
$steam->login();
```

Logout
```php
$steam->logout();
```

Update
```php
$steam->update();
```

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
if($steam->IsAuth())
{
  var_dump($_SESION['steamuser']);
} else {
  echo "NOT AUTHED";
}
```

Login
```php
SteamAuth::login();
```

Logout
```php
SteamAuth::logout();
```

Update
```php
SteamAuth::update();
```

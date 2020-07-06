Download Repository and unpack files (steamauth.php and openid.php)

Require and use lib

```php
require('steamauth.php');

use nod3zp\Auth\SteamAuth;
use nod3zp\Auth\Userinfo;
```

Check if user Auth:
```php
if(SteamAuth::IsAuth())
{
  $userinfo = new Userinfo();
  echo "AUTHED: $userinfo->personaname";
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

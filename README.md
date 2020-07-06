Download Repository and unpack files (steamauth.php and openid.php)

Require and use lib

```php
require('steamauth.php');

use nod3zp\Auth\SteamAuth;
use nod3zp\Auth\Userinfo;
```

Set Config in **steamauth.php**
```php
class Config
{
    public $apikey = ''; //API KEY FROM https://steamcommunity.com/dev/apikey
    public $domainname = 'localhost'; //SERVER DOMAIN
    public $logoutpage = '/'; //PAGE AFTER LOGOUT
    public $loginpage = '/'; //PAGE AFTER LOGIN
}
```

User status
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

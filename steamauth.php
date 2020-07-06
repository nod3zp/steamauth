<?php

namespace nod3zp\Auth;

ob_start();
session_start();

class Config
{
    public $apikey = '692388F6DA6B8C593853AE7ED8A82D2F';
    public $domainname = 'localhost';
    public $logoutpage = '/';
    public $loginpage = '/';
}

class Userinfo
{
    public $steamid;
    public $communityvisibilitystate;
    public $profilestate;
    public $personaname;
    public $lastlogoff;
    public $profileurl;
    public $avatar;
    public $avatarmedium;
    public $avatarfull;
    public $personastate;
    public $realname;
    public $primaryclanid;
    public $timecreated;
    public $uptodate;

    public function __construct()
    {
        $config = new Config();
        if (empty($_SESSION['steam_uptodate']) or empty($_SESSION['steam_personaname'])) {
            $url = file_get_contents("https://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key=".$config->apikey."&steamids=".$_SESSION['steamid']); 
            $content = json_decode($url, true);
            $_SESSION['steam_steamid'] = $content['response']['players'][0]['steamid'];
            $_SESSION['steam_communityvisibilitystate'] = $content['response']['players'][0]['communityvisibilitystate'];
            $_SESSION['steam_profilestate'] = $content['response']['players'][0]['profilestate'];
            $_SESSION['steam_personaname'] = $content['response']['players'][0]['personaname'];
            $_SESSION['steam_lastlogoff'] = $content['response']['players'][0]['lastlogoff'];
            $_SESSION['steam_profileurl'] = $content['response']['players'][0]['profileurl'];
            $_SESSION['steam_avatar'] = $content['response']['players'][0]['avatar'];
            $_SESSION['steam_avatarmedium'] = $content['response']['players'][0]['avatarmedium'];
            $_SESSION['steam_avatarfull'] = $content['response']['players'][0]['avatarfull'];
            $_SESSION['steam_personastate'] = $content['response']['players'][0]['personastate'];
            if (isset($content['response']['players'][0]['realname'])) { 
                   $_SESSION['steam_realname'] = $content['response']['players'][0]['realname'];
               } else {
                   $_SESSION['steam_realname'] = "Real name not given";
            }
            $_SESSION['steam_primaryclanid'] = $content['response']['players'][0]['primaryclanid'];
            $_SESSION['steam_timecreated'] = $content['response']['players'][0]['timecreated'];
            $_SESSION['steam_uptodate'] = time();
        }

        $this->steamid = $_SESSION['steam_steamid'];
        $this->communityvisibilitystate = $_SESSION['steam_communityvisibilitystate'];
        $this->profilestate = $_SESSION['steam_profilestate'];
        $this->personaname = $_SESSION['steam_personaname'];
        $this->lastlogoff = $_SESSION['steam_lastlogoff'];
        $this->profileurl = $_SESSION['steam_profileurl'];
        $this->avatar = $_SESSION['steam_avatar'];
        $this->avatarmedium = $_SESSION['steam_avatarmedium'];
        $this->avatarfull = $_SESSION['steam_avatarfull'];
        $this->personastate = $_SESSION['steam_personastate'];
        $this->realname = $_SESSION['steam_realname'];
        $this->primaryclanid = $_SESSION['steam_primaryclanid'];
        $this->timecreated = $_SESSION['steam_timecreated'];
        $this->uptodate = $_SESSION['steam_uptodate'];
    }
}

class SteamAuth
{
    public static function isAuth()
    {
        return isset($_SESSION['steamid']);
    }

    public static function login()
    {
        $config = new Config();
        require(__DIR__ . '/openid.php');
        try {
            $openid = new LightOpenID($config->domainname);
            
            if(!$openid->mode) {
                $openid->identity = 'https://steamcommunity.com/openid';
                header('Location: ' . $openid->authUrl());
            } elseif ($openid->mode == 'cancel') {
                echo 'User has canceled authentication!';
            } else {
                if($openid->validate()) { 
                    $id = $openid->identity;
                    $ptn = "/^https?:\/\/steamcommunity\.com\/openid\/id\/(7[0-9]{15,25}+)$/";
                    preg_match($ptn, $id, $matches);
                    
                    $_SESSION['steamid'] = $matches[1];
                    if (!headers_sent()) {
                        header('Location: '.$config->loginpage);
                        exit;
                    } else {
                        ?>
                        <script type="text/javascript">
                            window.location.href="<?=$config->loginpage?>";
                        </script>
                        <noscript>
                            <meta http-equiv="refresh" content="0;url=<?=$config->loginpage?>" />
                        </noscript>
                        <?php
                        exit;
                    }
                } else {
                    echo "User is not logged in.\n";
                }
            }
        } catch(ErrorException $e) {
            echo $e->getMessage();
        }
    }

    public static function logout()
    {
        $config = new Config();
        session_unset();
        session_destroy();
        header('Location: '.$config->logoutpage);
    }

    public static function update()
    {
        unset($_SESSION['steam_uptodate']);
        header('Location: '.$_SERVER['PHP_SELF']);
    }
}

?>
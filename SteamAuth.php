<?php

namespace nod3zp;

require_once(__DIR__ . '/openid.php');
use LightOpenID;

ob_start();
session_start();

class SteamAuth
{
    private $domain;
    private $apikey;
    private $logoutpage;
    private $loginpage;

    public function __construct($config=[])
    {
        if(!isset($config['apikey']))
        {
            exit('[SteamAuth] [Config] Field "apikey" was empty!');
        }
        if(!isset($config['domain']))
        {
            exit('[SteamAuth] [Config] Field "domain" was empty!');
        }
        if(!isset($config['logoutpage']))
        {
            exit('[SteamAuth] [Config] Field "logoutpage" was empty!');
        }
        if(!isset($config['loginpage']))
        {
            exit('[SteamAuth] [Config] Field "loginpage" was empty!');
        }
        $this->domain = $config['domain'];
        $this->apikey = $config['apikey'];
        $this->logoutpage = $config['logoutpage'];
        $this->loginpage = $config['loginpage'];

        if ((!isset($_SESSION['steam_uptodate']) || !isset($_SESSION['steamuser'])) && isset($_SESSION['steamid'])) {
            $url = file_get_contents("https://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key=".$this->apikey."&steamids=".$_SESSION['steamid']); 
            $content = json_decode($url, true);
            $_SESSION['steamuser'] = $content['response']['players'][0];
            $_SESSION['steam_uptodate'] = time();
        }
    }

    public function isAuth()
    {
        return (isset($_SESSION['steamid']) && isset($_SESSION['steamuser']));
    }

    public function login()
    {
        try {
            $openid = new LightOpenID($this->domain);
            
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
                        header('Location: '.$this->loginpage);
                        exit;
                    } else {
                        ?>
                        <script type="text/javascript">
                            window.location.href="<?=$this->loginpage?>";
                        </script>
                        <noscript>
                            <meta http-equiv="refresh" content="0;url=<?=$this->loginpage?>" />
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

    public function logout()
    {
        session_unset();
        session_destroy();
        header('Location: '.$this->logoutpage);
    }

    public function update()
    {
        unset($_SESSION['steam_uptodate']);
        header('Location: '.$_SERVER['REQUEST_URI']);
    }
}

?>

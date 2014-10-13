<!DOCTYPE html>

<?php
    
    set_time_limit(0);
    ini_set('default_socket_timeout', 300);

    define("clientID", '0b592a8b2ccd4ea18fe84f4509e64ebc');
    define("clientSecret", '8b48d0165e394ec09a9317e67d1f9575');
    define("websiteURL", 'http://lancemadden.us/superdash/index.php');
    define("redirectURI", 'http://lancemadden.us/superdash/index.php');


    function connect_instagram($url) {
        $ch = curl_init($url);
        
        curl_setopt_array($ch, array(
            CURLOPT_URL  =>  $url,
            CURLOPT_RETURNTRANSFER  =>  true, 
            CURLOPT_SSL_VERIFYHOST  =>  false, 
            CURLOPT_SSL_VERIFYPEER  =>  false
        ));
        
        $result = curl_exec($ch);
        curl_close($ch);
        
        return $result;
    }
    
    function get_userid_instagram($username) {
        
        $url = "https://api.instagram.com/v1/users/search?q=".$username."&client_id=".clientID;
        $instagramData = connect_instagram($url);
        $results = json_decode($instagramData, true);
        
        return $results['data'][0]['id'];
    }
    
    function print_instagram_images($userID) {
        $url = "https://api.instagram.com/v1/users/".$userID."/media/recent?client_id=".clientID."&count=-1";
        
        
        $instagramData = connect_instagram($url);
        $results = json_decode($instagramData, true);
        
        $fourCount = 0;
        foreach ($results['data'] as $item) {
            echo "<img src='".$item['images']['low_resolution']['url']."' /> ";
            
            if ($fourCount == 3) {
                echo "<br />";
            }
            
            $fourCount += ($fourCount == 3 ? -3 : 1);
        }
        
    }
    
    function print_instagram_likes($token) {
        $url = "https://api.instagram.com/v1/users/self/media/liked?count=8&access_token=$token";
        //$url = "https://api.instagram.com/v1/users/".$userID."/media/recent?client_id=".clientID."&count=-1";
        
        
        $instagramData = connect_instagram($url);
        $results = json_decode($instagramData, true);
        
        $fourCount = 0;
        foreach ($results['data'] as $item) {
            echo "<img src='".$item['images']['low_resolution']['url']."' /> ";
            
            //if ($fourCount == 3) {
            //    echo "<br />";
            //}
            
            //$fourCount += ($fourCount == 3 ? -3 : 1);
        }
    }
    
    function print_instagram_following($userID, $token) {
        $url = "https://api.instagram.com/v1/users/".$userID."/follows?access_token=".$token;
        
        $instagramData = connect_instagram($url);
        $results = json_decode($instagramData, true);
        
        $theEcho = "";
        $theEcho .= "<p>You follow:<br />";
        foreach($results['data'][0] as $item) {
            $theEcho .= "<b>".$item['data']['username']."</b><br />";
        }
        $theEcho .= "</p>";
        
        echo $theEcho;
        
    }
?>

<html>

    <head>
        <title>The Superdash</title>
        
        <!-- //////////////// ALL BOOTSTRAP LINKS \\\\\\\\\\\\\\\ -->
        <!-- Latest compiled and minified CSS -->
        <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
        
        <!-- Optional theme -->
        <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap-theme.min.css">
        
        <!-- Latest compiled and minified JavaScript -->
        <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
        <!-- //////////////////////////\\\\\\\\\\\\\\\\\\\\\\\\\\ -->
        
    </head>
    
    <body align='center'>
        <h1>The Superdash</h1>
            <a href="https://api.instagram.com/oauth/authorize/?client_id=<?php echo clientID; ?>&redirect_uri=<?php echo redirectURI; ?>&response_type=code">Login Instagram</a> |   
            <a href='javascript:void(0);'>Login Facebook (not working)</a> | 
            <a href='javascript:void(0);'>Login Twitter (not working)</a>
        
        <?php 
            if (isset($_GET['code'])) {
                $code = $_GET['code'];
                
                $url = "https://api.instagram.com/oauth/access_token";
                $access_token_settings = array(
                    'client_id'         =>      clientID,
                    'client_secret'     =>      clientSecret,
                    'grant_type'        =>      'authorization_code',
                    'redirect_uri'      =>      redirectURI,
                    'code'              =>      $code       
                );
                
                $curl = curl_init($url);
                curl_setopt($curl, CURLOPT_POST, true);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $access_token_settings);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
                
                $result = curl_exec($curl);
                curl_close($curl);
                
                $results = json_decode($result, true);
                
                $access_token = $results['access_token'];
                $_SESSION['oAuth'] = $access_token;
                
                echo "<br />".$results['user']['username']."<br />";
                
                $instagram_userid = get_userid_instagram($results['user']['username']);
                //print_instagram_images($instagram_userid);
                //print_instagram_following($instagram_userid, $access_token);
                print_instagram_likes($access_token);
                //print_instagram_followers();
                
            }
        ?>
    </body>
    
</html>

















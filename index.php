<?php

// Turns error reporting up to eleven
error_reporting(E_ALL^E_STRICT);
ini_set('display_errors', 1);

// Instagram API credentials
$ig_client_id = 'e9550fc2f7654d5ba248fc710462af7a';
$ig_secret    = '466d4d104a4449d896c5cc977e086379';

// Pusher API credentials
$pusher_key    = '867d60a8d5de3996dd25';
$pusher_secret = '7709ac1336e7968d1a61';
$pusher_app_id = '42771';

// Catches the Instagram realtime Pubsubhubub challenge flow
if (isset($_GET['hub_challenge'])) {
    echo $_GET['hub_challenge'];
    exit;
}

if ($_SERVER['REQUEST_METHOD']==='POST') {
    // Instantiates Pusher PHP API
    require 'lib/Pusher.php';

    $update = file_get_contents('php://input');

    $photos = json_decode($update);
    if (is_array($photos)) {
        $length = count($photos);
    }

    if ($length>0) {
        $pusher = new Pusher($pusher_key, $pusher_secret, $pusher_app_id);
        $pusher->trigger(
            'selfies', 
            'new-selfie', 
            array(
                'newcount' => $length,
            )
        );
    }
}

// Get 12 most recent IG photos
$url_params = array(
    'client_id' => $ig_client_id,
    'client_secret' => $ig_secret,
);

$ch = curl_init('https://api.instagram.com/v1/tags/selfie/media/recent');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($url_params));
$photos = curl_exec($ch);

/*

https://api.instagram.com/v1/tags/nofilter/media/recent

curl -F 'client_id=e9550fc2f7654d5ba248fc710462af7a' \
     -F 'client_secret=466d4d104a4449d896c5cc977e086379' \
     -F 'object=tag' \
     -F 'aspect=media' \
     -F 'object_id=selfie' \
     -F 'callback_url=http://demo.copterlabs.com/filive/workshop/' \
     https://api.instagram.com/v1/subscriptions/

curl -X DELETE 'https://api.instagram.com/v1/subscriptions?client_secret=466d4d104a4449d896c5cc977e086379&client_id=e9550fc2f7654d5ba248fc710462af7a&object=all'

*/

?>
<!doctype html>
<html lang="en">

<head>

<meta charset="utf-8" />

<title>Realtime Workshop by Jason Lengstorf &mdash; Future Insights Live</title>

</head>

<body>

<h1>Selfies!</h1>

<a href="https://api.instagram.com/oauth/authorize/?client_id=<?=$ig_client_id?>&amp;redirect_uri=http://demo.copterlabs.com/filive/workshop/login.php&amp;response_type=code">Login</a>

<div class="message hidden"></div>

<ul id="selfies">
    <li class="loading">No selfies yet&hellip; #sadface</li>
</ul><!--/#selfies-->

<pre>
Photos:
<?php var_dump($photos); ?> 
</pre>

<footer>
    <p>
        This demo was created by 
        <a href="http://www.lengstorf.com/">Jason Lengstorf</a> for use at the 
        realtime workshop at Future Insights Live 2013. It is released under the 
        MIT License.
    </p>
</footer>

<script src="http://code.jquery.com/jquery-2.0.0.min.js"></script>
<script src="http://js.pusher.com/2.0/pusher.min.js"></script>
<script>

jQuery(function($){

    // Enable pusher logging - don't include this in production
    Pusher.log = function(message) {
        if (window.console && window.console.log) window.console.log(message);
    };

    // Flash fallback logging - don't include this in production
    WEB_SOCKET_DEBUG = true;

    var newcount = 0,
        pusher   = new Pusher('867d60a8d5de3996dd25'),
        channel  = pusher.subscribe('selfies'),
        selfies  = $('selfies');

    channel.bind('new-selfie', function(data){

        newcount += data.newcount;

        console.log(newcount);

        // If the loading LI still exists, removes it
        // if (selfie_count===1) {
        //     for (var i=0; i<selfies.childNodes.length; i++) {
        //         if (selfies.childNodes[i].className==="loading") {
        //             selfies.removeChild(selfies.childNodes[i]);
        //         }
        //     }
        // }

        // Creates a new LI with the selfie
        //TODO: Add selfie markup
        // var li = document.createElement("li");
        // li.appendChild(data.selfie);
        // ul.appendChild(li);

    });

});

</script>

</body>

</html>


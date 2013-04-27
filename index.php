<?php

// Catches the Instagram realtime Pubsubhubub challenge flow
if (isset($_GET['hub.challenge'])) {
    echo $_GET['hub.challenge'];
    exit;
}

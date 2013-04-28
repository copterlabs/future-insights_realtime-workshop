<?php

// Catches the Instagram realtime Pubsubhubub challenge flow
if (isset($_GET['hub_challenge'])) {
    echo $_GET['hub_challenge'];
    exit;
}

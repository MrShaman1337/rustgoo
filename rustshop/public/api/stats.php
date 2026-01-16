<?php
declare(strict_types=1);
require_once __DIR__ . "/../../server/helpers.php";

init_db();

json_response([
    "orders_delivered" => get_site_stat("orders_delivered", 214),
    "active_players" => get_site_stat("active_players", 23)
]);

<?php
declare(strict_types=1);
require_once __DIR__ . "/../../../server/helpers.php";

start_session();
if (empty($_SESSION["user_id"])) {
    json_response(["ok" => false], 401);
}

$user = get_user_by_id((int)$_SESSION["user_id"]);
if (!$user) {
    user_logout();
    json_response(["ok" => false], 401);
}
if (!empty($user["is_banned"])) {
    user_logout();
    json_response(["error" => "User is banned"], 403);
}

json_response([
    "ok" => true,
    "user" => [
        "id" => (int)$user["id"],
        "steam_id" => $user["steam_id"],
        "steam_nickname" => $user["steam_nickname"],
        "steam_avatar" => $user["steam_avatar"],
        "steam_profile_url" => $user["steam_profile_url"]
    ]
]);

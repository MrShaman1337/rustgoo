<?php
declare(strict_types=1);
require_once __DIR__ . "/../../../server/helpers.php";

start_session();
rate_limit("steam_callback", 10, 60);

if (($_GET["openid_mode"] ?? "") === "cancel") {
    header("Location: /account");
    exit;
}

$openidParams = [];
foreach ($_GET as $key => $value) {
    if (strpos($key, "openid_") === 0) {
        $suffix = substr($key, 7);
        $openidParams["openid." . $suffix] = $value;
    }
}

if (empty($openidParams["openid.sig"])) {
    json_response(["error" => "Invalid OpenID response"], 400);
}

$openidParams["openid.mode"] = "check_authentication";
$payload = http_build_query($openidParams);
$context = stream_context_create([
    "http" => [
        "method" => "POST",
        "header" => "Content-Type: application/x-www-form-urlencoded",
        "content" => $payload,
        "timeout" => 5
    ]
]);
$raw = @file_get_contents(steam_openid_endpoint(), false, $context);
if ($raw === false || strpos($raw, "is_valid:true") === false) {
    json_response(["error" => "OpenID validation failed"], 401);
}

$claimed = $_GET["openid_claimed_id"] ?? "";
if (!preg_match("#^https?://steamcommunity\\.com/openid/id/(\\d+)$#", $claimed, $matches)) {
    json_response(["error" => "Invalid Steam ID"], 400);
}
$steamId = $matches[1];

$existing = get_user_by_steam_id($steamId);
if ($existing && !empty($existing["is_banned"])) {
    json_response(["error" => "User is banned"], 403);
}

$profile = fetch_steam_profile($steamId);
$user = upsert_user($steamId, $profile);
user_login($user);

header("Location: /account");
exit;

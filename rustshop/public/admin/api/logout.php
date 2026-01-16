<?php
declare(strict_types=1);
require_once __DIR__ . "/../../../server/helpers.php";

admin_logout();
json_response(["ok" => true]);

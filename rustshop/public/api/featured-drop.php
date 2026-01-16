<?php
declare(strict_types=1);
require_once __DIR__ . "/../../server/helpers.php";

init_db();

$drop = get_featured_drop();
if (!$drop || empty($drop["is_enabled"])) {
    json_response(["ok" => true, "featured_drop" => null]);
}

$productId = sanitize_text($drop["product_id"] ?? "");
if (!$productId) {
    json_response(["ok" => true, "featured_drop" => null]);
}

$products = load_products();
$product = null;
foreach ($products as $item) {
    if (($item["id"] ?? "") === $productId) {
        $product = $item;
        break;
    }
}
if (!$product || (($product["is_active"] ?? true) === false)) {
    json_response(["ok" => true, "featured_drop" => null]);
}

$title = sanitize_text($drop["title"] ?? "");
$subtitle = sanitize_text($drop["subtitle"] ?? "");
$cta = sanitize_text($drop["cta_text"] ?? "") ?: "Add VIP";
$price = floatval($drop["price"] ?? 0);
$oldPrice = $drop["old_price"] !== null ? floatval($drop["old_price"]) : null;

if ($price <= 0) {
    $price = floatval($product["price"] ?? 0);
}

json_response([
    "ok" => true,
    "featured_drop" => [
        "product_id" => $productId,
        "title" => $title !== "" ? $title : ($product["name"] ?? $product["title"] ?? "Featured Drop"),
        "subtitle" => $subtitle !== "" ? $subtitle : ($product["perks"] ?? $product["short_description"] ?? ""),
        "cta_text" => $cta,
        "price" => $price,
        "old_price" => $oldPrice,
        "is_enabled" => true,
        "product" => $product
    ]
]);

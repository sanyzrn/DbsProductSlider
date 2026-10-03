<?php
define('ABSPATH', __DIR__);
require dirname(__DIR__) . '/includes/class-pce-products.php';
if (PCE_Products::items([]) !== []) { throw new RuntimeException('Missing WooCommerce must return no cards safely.'); }
echo "WooCommerce absence check passed.\n";

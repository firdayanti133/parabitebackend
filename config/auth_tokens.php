<?php

return [
    'access_ttl' => (int) env('JWT_TTL', 4320),
    'refresh_ttl' => (int) env('JWT_REFRESH_TTL', 20160),
    'blacklist_enabled' => (bool) env('JWT_BLACKLIST_ENABLED', true),
];

<?php

namespace App\Support;

class AssetUrl
{
    private const ASSET_FIELDS = ['image', 'menu_image', 'photo'];

    public static function transform(mixed $data, ?string $field = null): mixed
    {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $data[$key] = self::transform($value, is_string($key) ? $key : null);
            }

            return $data;
        }

        if (! is_string($data) || ! in_array($field, self::ASSET_FIELDS, true)) {
            return $data;
        }

        return self::toPublicUrl($data);
    }

    public static function toPublicUrl(?string $value): ?string
    {
        $value = trim((string) $value);

        if ($value === '') {
            return null;
        }

        $path = parse_url($value, PHP_URL_PATH);
        $path = ltrim(str_replace('\\', '/', is_string($path) ? $path : $value), '/');
        $diskPath = str_starts_with($path, 'storage/') ? substr($path, 8) : $path;

        if (str_starts_with($diskPath, 'img/menu/')) {
            return route('assets.menu.show', ['filename' => basename($diskPath)]);
        }

        if (filter_var($value, FILTER_VALIDATE_URL)) {
            return $value;
        }

        return url($path);
    }
}

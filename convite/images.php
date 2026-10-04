<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/config/assets.php';

/** Ordem das fases do convite (URLs com cache-bust). */
function convite_image_urls(): array
{
    return [
        sizo_asset('convite/assets/fase-1-marciano-marta.jpg'),
        sizo_asset('convite/assets/fase-2-escritura.jpg'),
        sizo_asset('convite/assets/fase-3-convidado.jpg'),
        sizo_asset('convite/assets/fase-4-programa.jpg'),
        sizo_asset('convite/assets/fase-5-presenca.jpg'),
    ];
}

function convite_inject_images(string $html): string
{
    $json = json_encode(convite_image_urls(), JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
    if ($json === false) {
        return $html;
    }

    return str_replace('__CONVITE_IMAGES_JSON__', $json, $html);
}

<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/config/assets.php';

/** Imagens 1→5 na mesma ordem dos ficheiros originais. Nome do convidado no slide 2 (imagem 2). */
function convite_image_urls(): array
{
    return [
        sizo_asset('convite/assets/fase-1.jpg'),
        sizo_asset('convite/assets/fase-2.jpg'),
        sizo_asset('convite/assets/fase-3.jpg'),
        sizo_asset('convite/assets/fase-4.jpg'),
        sizo_asset('convite/assets/fase-5.jpg'),
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

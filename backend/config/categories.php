<?php
/**
 * Ícones e cores permitidos nas categorias.
 *
 * O banco guarda só a chave (ex.: icon = 'smartphone', color = 'sky');
 * o backend recusa ao salvar qualquer valor fora destas listas.
 * O front transforma a chave no visual (frontend/components/name-cell.php):
 * - ícone: frontend/assets/icons/<chave>.svg
 * - cor: fundo no tom 200 e ícone no tom 400 da paleta (ex.: bg-sky-200 e bg-sky-400)
 * O primeiro item de cada lista é o padrão quando o valor salvo não é válido.
 * Para adicionar um ícone, coloque o SVG na pasta e a chave no grupo dele.
 */

// Grupos do seletor de ícones (frontend/components/icon-picker.php), na ordem do Figma
$iconGroups = [
    'Alimentação' => [
        'grocery',
        'restaurant',
        'fastfood',
        'local-cafe',
        'local-pizza',
        'bakery-dining',
        'icecream',
        'local-bar',
    ],
    'Eletrônicos' => [
        'smartphone',
        'laptop-windows',
        'tv',
        'headphones',
        'sports-esports',
        'photo-camera',
        'tablet',
        'earbuds',
    ],
    'Moda e acessórios' => [
        'apparel',
        'checkroom',
        'styler',
        'wallet',
        'eyeglasses',
        'watch',
        'diamond',
        'backpack',
    ],
    'Casa' => [
        'weekend',
        'bed',
        'chair',
        'table-lamp',
        'kitchen',
        'microwave-gen',
        'blender',
        'cleaning-services',
    ],
];

return [
    'icon_groups' => $iconGroups,
    // box-seam é só o padrão: não aparece no seletor
    'icons' => array_merge(['box-seam'], ...array_values($iconGroups)),
    'colors' => [
        'gray',
        'sky',
        'purple',
        'pink',
        'green',
        'red',
        'blue',
    ],
];

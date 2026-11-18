<?php
/**
 * Ícones e cores permitidos nas categorias.
 *
 * O banco guarda só a chave (ex.: icon = 'smartphone', color = 'sky-200');
 * o backend recusa ao salvar qualquer valor fora destas listas.
 * O front transforma a chave no visual (frontend/components/name-cell.php):
 * - ícone: frontend/assets/icons/<chave>.svg
 * - cor: o tom escolhido é o fundo; o tom do ícone e da barra de % vem de icon_tones e bar_tones
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

// Grupos do seletor de cor (frontend/components/color-picker.php): rótulo => cor da paleta
$colorGroups = [
    'Azul' => 'sky',
    'Rosa' => 'pink',
    'Roxo' => 'purple',
    'Verde' => 'green',
    'Vermelho' => 'red',
];
$colorTones = ['100', '200', '300', '400', '500'];

// gray-200 é só o padrão: não aparece no seletor
$colors = ['gray-200'];
foreach ($colorGroups as $colorFamily) {
    foreach ($colorTones as $colorTone) {
        $colors[] = "$colorFamily-$colorTone";
    }
}

return [
    'icon_groups' => $iconGroups,
    // box-seam é só o padrão: não aparece no seletor
    'icons' => array_merge(['box-seam'], ...array_values($iconGroups)),
    'color_groups' => $colorGroups,
    'color_tones' => $colorTones,
    'colors' => $colors,
    // Tom do ícone para cada tom de fundo ('white' = ícone branco)
    'icon_tones' => [
        '100' => '400',
        '200' => '500',
        '300' => 'white',
        '400' => 'white',
        '500' => '200',
    ],
    // Tom da barra de %: o mais escuro entre o fundo e o ícone
    'bar_tones' => [
        '100' => '400',
        '200' => '500',
        '300' => '300',
        '400' => '400',
        '500' => '500',
    ],
];

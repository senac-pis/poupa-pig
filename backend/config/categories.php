<?php
/**
 * Ícones e cores permitidos nas categorias.
 *
 * O banco guarda só a chave (ex.: icon = 'phone-vibrate', color = 'sky');
 * o backend recusa ao salvar qualquer valor fora destas listas.
 * O front transforma a chave no visual (frontend/components/name-cell.php):
 * - ícone: frontend/assets/icons/<chave>.svg
 * - cor: fundo no tom 200 e ícone no tom 400 da paleta (ex.: bg-sky-200 e bg-sky-400)
 * O primeiro item de cada lista é o padrão quando o valor salvo não é válido.
 * Para adicionar um ícone, coloque o SVG na pasta e a chave aqui.
 */

return [
    'icons' => [
        'box-seam',
        'phone-vibrate',
        'eyeglasses',
    ],
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

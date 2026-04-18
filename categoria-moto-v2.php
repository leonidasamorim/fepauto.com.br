<?php
$categorias = [
    'Moto Iniciante'                                 => 'MOTO INICIANTE',
    'Iniciante'                                      => 'INICIANTE',
    'Novato'                                         => 'NOVATO',
    'Intermediário'                                  => 'INTERMEDIÁRIO',
    'Over45'                                         => 'OVER45',
    'Graduado'                                       => 'GRADUADO',
    'BNE-Graduado'                                   => 'BRASIL NORTE/NORDESTE DE ENDURO - GRADUADO',
    'BNE-Over45'                                     => 'BRASIL NORTE/NORDESTE DE ENDURO - OVER45',
    'BNE-Intermediário'                              => 'BRASIL NORTE/NORDESTE DE ENDURO - INTERMEDIÁRIO',
];
foreach ($categorias as $val => $label) {
    echo '<option value="' . htmlspecialchars($val) . '">' . htmlspecialchars($label) . "</option>\n";
}

<?php
$categorias = [
    'Moto Iniciante'                                 => 'MOTO INICIANTE',
    'Novato'                                         => 'NOVATO',
    'Intermediário'                                  => 'INTERMEDIÁRIO',
    'Over45'                                         => 'OVER45',
    'Graduado'                                       => 'GRADUADO'
];
foreach ($categorias as $val => $label) {
    echo '<option value="' . htmlspecialchars($val) . '">' . htmlspecialchars($label) . "</option>\n";
}

<?php
$categorias = [
    'Novato'                                         => 'NOVATO',
    'Intermediário'                                  => 'INTERMEDIÁRIO',
    'Over45'                                         => 'OVER45',
    'Graduado'                                       => 'GRADUADO',
    'Quadriculo'                                     => 'QUADRICULO'
];
foreach ($categorias as $val => $label) {
    echo '<option value="' . htmlspecialchars($val) . '">' . htmlspecialchars($label) . "</option>\n";
}

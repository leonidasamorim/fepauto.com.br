<?php
$categorias = [
    'Quadriciclo – NOVATO'       => 'Quadriciclo – NOVATO',
    'Quadriciclo – INTERMEDIÁRIO'   => 'Quadriciclo – INTERMEDIÁRIO',
    'Quadriciclo – OVER 45'       => 'Quadriciclo – OVER 45',
    'Quadriciclo – GRADUADO'     => 'Quadriciclo – GRADUADO',
];
foreach ($categorias as $val => $label) {
    echo "<option value=\"" . htmlspecialchars($val) . "\">" . htmlspecialchars($label) . "</option>\n";
}

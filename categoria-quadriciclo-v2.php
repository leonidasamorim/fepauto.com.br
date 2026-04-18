<?php
$categorias = [
    'Quadriciclo – NOVATO'       => 'Quadriciclo – NOVATO',
    'Quadriciclo – EXPERIENTE'   => 'Quadriciclo – EXPERIENTE',
];
foreach ($categorias as $val => $label) {
    echo "<option value=\"" . htmlspecialchars($val) . "\">" . htmlspecialchars($label) . "</option>\n";
}

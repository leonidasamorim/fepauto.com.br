<?php
$categorias = [
    'Quad_Prod'  => 'Quadriciclo – Produção',
    'Quad_Mod'   => 'Quadriciclo – Modificado',
    'Quad_Nov'   => 'Quadriciclo – Novato',
];
foreach ($categorias as $val => $label) {
    echo "<option value=\"" . htmlspecialchars($val) . "\">" . htmlspecialchars($label) . "</option>\n";
}

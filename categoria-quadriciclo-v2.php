<?php
$categorias = [
   'Moto Iniciante'  => 'MOTO INICIANTE',
];
foreach ($categorias as $val => $label) {
    echo "<option value=\"" . htmlspecialchars($val) . "\">" . htmlspecialchars($label) . "</option>\n";
}

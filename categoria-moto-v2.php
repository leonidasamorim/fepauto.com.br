<?php
$categorias = [
    'Pro'       => 'Pro',
    'Expert'    => 'Expert',
    'Amateur'   => 'Amador',
    'Adventure' => 'Adventure',
    'Novato'    => 'Novato',
    'Senior'    => 'Sênior (40+)',
];
foreach ($categorias as $val => $label) {
    echo "<option value=\"" . htmlspecialchars($val) . "\">" . htmlspecialchars($label) . "</option>\n";
}

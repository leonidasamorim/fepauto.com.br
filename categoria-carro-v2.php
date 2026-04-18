<?php
$categorias = [
    'UTV'                    => 'UTV',
    'EXPEDIÇÃO'              => 'EXPEDIÇÃO',
    'GRADUADOS OFF-ROAD 4X4' => 'GRADUADOS OFF-ROAD 4X4',
    'STREET 4X2 LIVRE'       => 'STREET 4X2 LIVRE'
];
foreach ($categorias as $val => $label) {
    echo "<option value=\"" . htmlspecialchars($val) . "\">" . htmlspecialchars($label) . "</option>\n";
}
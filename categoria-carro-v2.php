<?php
$categorias = [
    'Overall'          => 'Overall – Piloto',
    'T1'               => 'T1 – Produção',
    'T2'               => 'T2 – Modificado Leve',
    'T3'               => 'T3 – Modificado Pesado',
    'T4'               => 'T4 – Extremo',
    'Turismo'          => 'Turismo',
    'UTV_Prod'         => 'UTV – Produção',
    'UTV_Mod'          => 'UTV – Modificado',
    'Classic'          => 'Classic (até 1990)',
];
foreach ($categorias as $val => $label) {
    echo "<option value=\"" . htmlspecialchars($val) . "\">" . htmlspecialchars($label) . "</option>\n";
}

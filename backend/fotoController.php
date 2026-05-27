<?php
/**
 * fotoController.php
 * Returns the N most recent photos for a given day folder.
 *
 * Usage (JSON endpoint):
 *   GET backend/fotoController.php?dag=3&limit=10
 *
 *   dag   : 0–6 (folder index matching 0_Zondag … 6_Zaterdag)
 *   limit : number of photos to return (default 10)
 */

header('Content-Type: application/json');
header('Cache-Control: no-store');

// --- map index → folder name ---
$dagFolders = [
    0 => '0_Zondag',
    1 => '1_Maandag',
    2 => '2_Dinsdag',
    3 => '3_Woensdag',
    4 => '4_Donderdag',
    5 => '5_Vrijdag',
    6 => '6_Zaterdag',
];

$dagNamen = [
    0 => 'Zondag',
    1 => 'Maandag',
    2 => 'Dinsdag',
    3 => 'Woensdag',
    4 => 'Donderdag',
    5 => 'Vrijdag',
    6 => 'Zaterdag',
];

$dag    = isset($_GET['dag'])   ? (int)$_GET['dag']   : 3;
$limit  = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
$offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
$time   = isset($_GET['time'])  ? $_GET['time']        : null;

if (!array_key_exists($dag, $dagFolders)) {
    http_response_code(400);
    echo json_encode(['error' => 'Ongeldig dag-nummer']);
    exit;
}

$baseDir  = dirname(__DIR__) . '/fotos/';
$folder   = $baseDir . $dagFolders[$dag];
$webBase  = 'fotos/' . $dagFolders[$dag] . '/';

if (!is_dir($folder)) {
    http_response_code(404);
    echo json_encode(['error' => 'Map niet gevonden: ' . $folder]);
    exit;
}

// --- collect & parse filenames: HH_MM_SS_idXXXX.jpg ---
$fotos = [];

foreach (glob($folder . '/*.jpg') as $pad) {
    $bestand = basename($pad);
    // Pattern: HH_MM_SS_idNNNN.jpg
    if (preg_match('/^(\d{2})_(\d{2})_(\d{2})_id(\d+)\.jpg$/i', $bestand, $m)) {
        $tijdSeconden = (int)$m[1] * 3600 + (int)$m[2] * 60 + (int)$m[3];
        $fotos[] = [
            'bestand'      => $bestand,
            'pad'          => $webBase . $bestand,
            'tijdLabel'    => $m[1] . ':' . $m[2] . ':' . $m[3],   // HH:MM:SS
            'tijdSeconden' => $tijdSeconden,
            'id'           => (int)$m[4],
            'dag'          => $dagNamen[$dag],
        ];
    }
}

$totaalFotos = count($fotos);

// --- Sorting ---
if ($time && preg_match('/^(\d{2}):(\d{2}):(\d{2})$/', $time, $tm)) {
    $doelSeconden = (int)$tm[1] * 3600 + (int)$tm[2] * 60 + (int)$tm[3];

    // Only keep photos at or before the current time (no future photos)
    $fotos = array_filter($fotos, fn($f) => $f['tijdSeconden'] <= $doelSeconden);
    $fotos = array_values($fotos);

    // Sort by proximity to the requested time (closest first)
    usort($fotos, function($a, $b) use ($doelSeconden) {
        $diffA = $doelSeconden - $a['tijdSeconden'];
        $diffB = $doelSeconden - $b['tijdSeconden'];
        return $diffA - $diffB;
    });
} else {
    // Default: NEWEST first (highest tijdSeconden first)
    usort($fotos, fn($a, $b) => $b['tijdSeconden'] - $a['tijdSeconden']);
}

// Apply offset and slice to requested limit
$fotos = array_slice($fotos, $offset, $limit);

echo json_encode([
    'dag'    => $dagNamen[$dag],
    'totaal' => $totaalFotos,
    'offset' => $offset,
    'fotos'  => array_values($fotos),
], JSON_UNESCAPED_UNICODE);
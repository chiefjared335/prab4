<?php
// ── API: return JSON list of 10 photos closest to current Dutch time ────────
if (isset($_GET['api']) && $_GET['api'] === '1') {
    header('Content-Type: application/json');

    $fotosDir  = __DIR__ . '/fotos/';
    $allowed   = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $photos    = [];
    $filterDay = isset($_GET['day']) ? basename($_GET['day']) : null;

    if (is_dir($fotosDir)) {
        foreach (glob($fotosDir . '*', GLOB_ONLYDIR) as $dayDir) {
            $day = basename($dayDir);
            if ($filterDay && $day !== $filterDay)
                continue;

            foreach (glob($dayDir . '/*') as $file) {
                $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                if (!in_array($ext, $allowed))
                    continue;

                $filename = basename($file);

                // Parse time from filename e.g. 12_15_00_id2125.jpg (already Dutch time)
                preg_match('/^(\d+)_(\d+)_(\d+)_id(\d+)/', $filename, $m);
                $id = $m[4] ?? 'unknown';

                if (isset($m[1])) {
                    // Filename time is already Dutch time — use as-is
                    $timeString   = sprintf('%02d:%02d:%02d', $m[1], $m[2], $m[3]);
                    $photoSeconds = (int)$m[1] * 3600 + (int)$m[2] * 60 + (int)$m[3];
                } else {
                    $timeString   = '--:--:--';
                    $photoSeconds = -1;
                }

                $photos[] = [
                    'filename'     => $day . '/' . $filename,
                    'mtime'        => filemtime($file),
                    'photoSeconds' => $photoSeconds,
                    'time'         => [
                        'timeString' => $timeString,
                        'id'         => $id,
                        'day'        => $day,
                    ],
                ];
            }
        }
    }

    // Current Dutch time in seconds since midnight
    $nowDt  = new DateTime('now', new DateTimeZone('Europe/Amsterdam'));
    $nowSec = (int)$nowDt->format('G') * 3600 + (int)$nowDt->format('i') * 60 + (int)$nowDt->format('s');

    // Sort by proximity to current Dutch time (closest first)
    usort($photos, function ($a, $b) use ($nowSec) {
        $diffA = abs($a['photoSeconds'] - $nowSec);
        $diffB = abs($b['photoSeconds'] - $nowSec);
        return $diffA - $diffB;
    });
    $photos = array_slice($photos, 0, 10);

    echo json_encode($photos);
    exit;
}

// ── PHOTO SERVE ─────────────────────────────────────────────────────────────
if (isset($_GET['photo'])) {
    $fotosDir  = __DIR__ . '/fotos/';
    $requested = realpath($fotosDir . $_GET['photo']);
    $base      = realpath($fotosDir);

    if ($requested && $base && strpos($requested, $base) === 0 && file_exists($requested)) {
        $ext  = strtolower(pathinfo($requested, PATHINFO_EXTENSION));
        $mime = match ($ext) {
            'jpg', 'jpeg' => 'image/jpeg',
            'png'         => 'image/png',
            'gif'         => 'image/gif',
            'webp'        => 'image/webp',
            default       => 'application/octet-stream',
        };
        header('Content-Type: ' . $mime);
        readfile($requested);
    } else {
        http_response_code(404);
        echo 'Photo not found';
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <style>
        body {
            margin: 0;
            font-family: monospace;
            background: #0c0c0e;
            color: #f0f0f0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            gap: 16px;
        }

        h1 { font-size: 2rem; letter-spacing: -1px; }

        nav { display: flex; gap: 24px; }

        nav a {
            color: #e8ff47;
            text-decoration: none;
            font-size: 0.85rem;
            letter-spacing: 2px;
            text-transform: uppercase;
        }

        nav a:hover { text-decoration: underline; }
    </style>
</head>

<body>
    <h1>Home</h1>
    <nav>
        <a href="index.php">Home</a>
        <a href="foto_page.php">Foto Page</a>
    </nav>
    <h3>Welkom bij de Fotokiosk!</h3>
</body>

</html>
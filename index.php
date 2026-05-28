<?php
session_start();

// Winkelwagen initialiseren
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$cartCount = array_sum(array_column($_SESSION['cart'], 'quantity'));

$folders = [
  "0_zondag",
  "1_maandag",
  "2_dinsdag",
  "3_woensdag",
  "4_donderdag",
  "5_vrijdag",
  "6_zaterdag"
];

function getImages($folder) {
    $path = __DIR__ . "/fotos/" . $folder;
    $images = [];

    if (is_dir($path)) {
        foreach (scandir($path) as $file) {
            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));

            if (in_array($ext, ["jpg", "jpeg", "png"])) {
                $images[] = "fotos/$folder/$file";
            }
        }
    }

    sort($images);
    return $images;
}

$data = [];
foreach ($folders as $f) {
    $data[$f] = getImages($f);
}

$dagNamen = ['Zondag','Maandag','Dinsdag','Woensdag','Donderdag','Vrijdag','Zaterdag'];
$huidigeDag = (int)date('w');
?>

<!DOCTYPE html>
<html lang="nl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>FEED – Fotokiosk</title>

<style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    :root {
        --accent:   #caff00;
        --bg:       #111111;
        --surface:  #1a1a1a;
        --border:   #2a2a2a;
        --text:     #ffffff;
        --muted:    #888888;
    }

    body {
        background: var(--bg);
        color: var(--text);
        font-family: 'Arial Black', Arial, sans-serif;
        min-height: 100vh;
    }

    /* ── NAV ── */
    nav {
        display: flex;
        align-items: center;
        gap: 24px;
        padding: 0 28px;
        height: 56px;
        background: var(--bg);
        border-bottom: 1px solid var(--border);
    }
    nav a {
        color: var(--muted);
        text-decoration: none;
        font-size: 12px;
        letter-spacing: .08em;
        text-transform: uppercase;
    }
    nav a.active { color: var(--accent); }
    .cart-btn {
        margin-left: auto;
        display: flex;
        align-items: center;
        gap: 8px;
        border: 1px solid var(--border);
        padding: 8px 16px;
        border-radius: 4px;
        font-size: 12px;
        color: var(--text);
        text-decoration: none;
        letter-spacing: .06em;
        text-transform: uppercase;
    }
    .cart-badge {
        background: var(--accent);
        color: #000;
        border-radius: 50%;
        width: 20px; height: 20px;
        display: flex; align-items: center; justify-content: center;
        font-size: 11px; font-weight: 900;
    }

    /* ── HEADER ── */
    header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        padding: 24px 28px 0;
    }
    .logo {
        font-size: 96px;
        font-weight: 900;
        line-height: 1;
        color: var(--accent);
        letter-spacing: -.02em;
        text-transform: uppercase;
    }
    .clock-block { text-align: right; }
    #liveClock {
        font-size: 40px;
        font-weight: 900;
        color: var(--accent);
        letter-spacing: .04em;
        font-variant-numeric: tabular-nums;
    }
    .clock-meta {
        font-size: 11px;
        color: var(--muted);
        letter-spacing: .1em;
        text-transform: uppercase;
        margin-top: 4px;
    }

    /* ── DAG TABS ── */
    .dag-tabs {
        display: flex;
        gap: 6px;
        padding: 20px 28px 0;
    }
    .dag-tabs button {
        padding: 8px 18px;
        border: 1px solid var(--border);
        border-radius: 4px;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: .06em;
        text-transform: uppercase;
        color: var(--text);
        background: transparent;
        cursor: pointer;
        font-family: inherit;
        transition: background .15s, border-color .15s;
    }
    .dag-tabs button:hover { border-color: var(--accent); }
    .dag-tabs button.active {
        background: var(--accent);
        border-color: var(--accent);
        color: #000;
    }

    /* ── STATUS BAR ── */
    .status-bar {
        padding: 14px 28px 4px;
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 12px;
        color: var(--muted);
    }
    .status-dot {
        width: 8px; height: 8px;
        border-radius: 50%;
        background: var(--accent);
    }
    .status-bar .sep { color: var(--border); }
    .progress-bar {
        height: 2px;
        background: var(--border);
        margin: 0 28px 16px;
        border-radius: 1px;
        overflow: hidden;
    }
    .progress-fill {
        height: 100%;
        width: 0%;
        background: var(--accent);
        transition: width linear;
    }

    /* ── FOTO GRID ── */
    .foto-grid {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 6px;
        padding: 0 28px 28px;
    }

    .foto-card {
        position: relative;
        aspect-ratio: 16/9;
        overflow: hidden;
        border-radius: 3px;
        background: var(--surface);
    }
    .foto-card img {
        width: 100%; height: 100%;
        object-fit: cover;
        display: block;
        transition: transform .25s;
    }
    .foto-card:hover img { transform: scale(1.04); }

    .foto-overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(to top, rgba(0,0,0,.7) 0%, transparent 55%);
        pointer-events: none;
    }

    .foto-badge-num {
        position: absolute;
        top: 8px; right: 8px;
        background: rgba(0,0,0,.55);
        color: var(--muted);
        font-size: 10px;
        font-weight: 700;
        padding: 2px 6px;
        border-radius: 2px;
        letter-spacing: .04em;
    }

    .foto-meta {
        position: absolute;
        bottom: 6px; left: 8px;
    }
    .foto-naam {
        color: var(--accent);
        font-size: 14px;
        font-weight: 900;
        letter-spacing: .02em;
    }
    .foto-info {
        color: var(--muted);
        font-size: 10px;
        letter-spacing: .04em;
        margin-top: 1px;
    }

    /* ── RESPONSIVE ── */
    @media(max-width:1000px) {
        .foto-grid { grid-template-columns: repeat(2, 1fr); }
        .logo { font-size: 56px; }
        header { flex-direction: column; gap: 12px; }
        .clock-block { text-align: left; }
        .dag-tabs { flex-wrap: wrap; }
    }
    @media(max-width:600px) {
        .foto-grid { grid-template-columns: 1fr; }
    }
</style>
</head>

<body>

<!-- NAV -->
<nav>
    <a href="index.php" class="active">Home</a>
    <a href="foto_page.php">Foto Page</a>
    <a href="winkelwagen.php" class="cart-btn">
        🛒 Cart
        <span class="cart-badge"><?= $cartCount ?></span>
    </a>
</nav>

<!-- HEADER -->
<header>
    <div class="logo">FEED</div>
    <div class="clock-block">
        <div id="liveClock">--:--:--</div>
        <div class="clock-meta">Live &bull; Nederlandse Tijd &bull; Elke 10s</div>
    </div>
</header>

<!-- DAG TABS -->
<div class="dag-tabs">
    <?php foreach ($dagNamen as $i => $naam): ?>
        <button class="daybtn" onclick="setDay(<?= $i ?>, this)"><?= htmlspecialchars($naam) ?></button>
    <?php endforeach; ?>
</div>

<!-- STATUS BAR -->
<div class="status-bar">
    <span class="status-dot"></span>
    <span id="currentDayText"></span>
    <span class="sep">&bull;</span>
    Volgende in <span id="count">10</span>s
    <span class="sep">&bull;</span>
    <span id="fotoCount">0</span> foto's geladen
</div>
<div class="progress-bar"><div class="progress-fill" id="progressFill"></div></div>

<!-- GRID (5 foto's) -->
<div class="foto-grid" id="fotoGrid">
    <div class="foto-card">
        <img id="img0" src="" alt="Foto 1">
        <div class="foto-overlay"></div>
        <div class="foto-badge-num">#1</div>
        <div class="foto-meta">
            <div class="foto-naam" id="label0"></div>
        </div>
    </div>
    <div class="foto-card">
        <img id="img1" src="" alt="Foto 2">
        <div class="foto-overlay"></div>
        <div class="foto-badge-num">#2</div>
        <div class="foto-meta">
            <div class="foto-naam" id="label1"></div>
        </div>
    </div>
    <div class="foto-card">
        <img id="img2" src="" alt="Foto 3">
        <div class="foto-overlay"></div>
        <div class="foto-badge-num">#3</div>
        <div class="foto-meta">
            <div class="foto-naam" id="label2"></div>
        </div>
    </div>
    <div class="foto-card">
        <img id="img3" src="" alt="Foto 4">
        <div class="foto-overlay"></div>
        <div class="foto-badge-num">#4</div>
        <div class="foto-meta">
            <div class="foto-naam" id="label3"></div>
        </div>
    </div>
    <div class="foto-card">
        <img id="img4" src="" alt="Foto 5">
        <div class="foto-overlay"></div>
        <div class="foto-badge-num">#5</div>
        <div class="foto-meta">
            <div class="foto-naam" id="label4"></div>
        </div>
    </div>
</div>

<script>
const data = <?php echo json_encode($data); ?>;
let selectedDay = new Date().getDay();
let index = 0;
let time = 10;

const folders = [
  "0_zondag",
  "1_maandag",
  "2_dinsdag",
  "3_woensdag",
  "4_donderdag",
  "5_vrijdag",
  "6_zaterdag"
];

const dagNamen = ['Zondag','Maandag','Dinsdag','Woensdag','Donderdag','Vrijdag','Zaterdag'];

// ── Dutch time helper ──
function getDutchTime() {
    const now = new Date();
    const fmt = new Intl.DateTimeFormat('nl-NL', {
        timeZone: 'Europe/Amsterdam',
        hour: '2-digit', minute: '2-digit', second: '2-digit',
        hour12: false
    });
    const parts = fmt.formatToParts(now);
    const h = parts.find(p => p.type === 'hour').value;
    const m = parts.find(p => p.type === 'minute').value;
    const s = parts.find(p => p.type === 'second').value;
    return `${h}:${m}:${s}`;
}

// ── Live Clock ──
function updateClock() {
    document.getElementById('liveClock').textContent = getDutchTime();
}
updateClock();
setInterval(updateClock, 1000);

// Set initial day
setDay(selectedDay, document.querySelectorAll(".daybtn")[selectedDay]);

function setDay(day, button) {
    selectedDay = day;
    index = 0;
    time = 10;

    document.querySelectorAll(".daybtn")
        .forEach(btn => btn.classList.remove("active"));
    button.classList.add("active");

    document.getElementById("currentDayText").textContent = dagNamen[day];

    updatePhotos();
    startProgressBar();
}

function updatePhotos() {
    const folder = folders[selectedDay];
    const list = data[folder];

    if (!list || list.length === 0) return;

    // Update foto count
    document.getElementById("fotoCount").textContent = list.length;

    // Fill 5 slots
    for (let i = 0; i < 5; i++) {
        const imgIndex = (index + i) % list.length;
        const src = list[imgIndex];
        document.getElementById("img" + i).src = src;

        // Extract filename for label
        const parts = src.split('/');
        const filename = parts[parts.length - 1];
        document.getElementById("label" + i).textContent = filename;
    }
}

function startProgressBar() {
    const fill = document.getElementById('progressFill');
    fill.style.transition = 'none';
    fill.style.width = '0%';
    fill.offsetWidth; // force reflow
    fill.style.transition = 'width 10s linear';
    fill.style.width = '100%';
}

function tick() {
    time--;
    document.getElementById("count").innerText = time;

    if (time <= 0) {
        index++;
        updatePhotos();
        time = 10;
        startProgressBar();
    }
}

updatePhotos();
startProgressBar();
setInterval(tick, 1000);
</script>

</body>
</html>
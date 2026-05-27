<?php
session_start();

// Winkelwagen initialiseren
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Foto aan winkelwagen toevoegen via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['foto'])) {
    $foto = htmlspecialchars($_POST['foto']);
    $id   = htmlspecialchars($_POST['id'] ?? '');
    $key  = 'foto_' . $id;
    if (!isset($_SESSION['cart'][$key])) {
        $_SESSION['cart'][$key] = ['foto' => $foto, 'id' => $id, 'quantity' => 1, 'price' => 0.25];
    } else {
        $_SESSION['cart'][$key]['quantity']++;
    }
}

$cartCount = array_sum(array_column($_SESSION['cart'], 'quantity'));

$dagNamen = ['Zondag','Maandag','Dinsdag','Woensdag','Donderdag','Vrijdag','Zaterdag'];
$huidigeDag = (int)date('w'); // 0=Zondag … 6=Zaterdag
$actieveDag = isset($_GET['dag']) ? (int)$_GET['dag'] : $huidigeDag;
if ($actieveDag < 0 || $actieveDag > 6) $actieveDag = $huidigeDag;
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FEED – Foto Page</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --accent:   #caff00;
            --bg:       #111111;
            --surface:  #1a1a1a;
            --border:   #2a2a2a;
            --text:     #ffffff;
            --muted:    #888888;
            --badge-bg: rgba(0,0,0,.55);
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
        .dag-tabs a {
            padding: 8px 18px;
            border: 1px solid var(--border);
            border-radius: 4px;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: .06em;
            text-transform: uppercase;
            color: var(--text);
            text-decoration: none;
            transition: background .15s, border-color .15s;
        }
        .dag-tabs a:hover { border-color: var(--accent); }
        .dag-tabs a.active {
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
            cursor: pointer;
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
            background: var(--badge-bg);
            color: var(--muted);
            font-size: 10px;
            font-weight: 700;
            padding: 2px 6px;
            border-radius: 2px;
            letter-spacing: .04em;
        }
        .foto-badge-latest {
            position: absolute;
            top: 8px; left: 8px;
            background: var(--accent);
            color: #000;
            font-size: 10px;
            font-weight: 900;
            padding: 3px 7px;
            border-radius: 2px;
            letter-spacing: .06em;
            text-transform: uppercase;
        }

        .foto-meta {
            position: absolute;
            bottom: 6px; left: 8px;
        }
        .foto-tijd {
            color: var(--accent);
            font-size: 18px;
            font-weight: 900;
            letter-spacing: .02em;
        }
        .foto-info {
            color: var(--muted);
            font-size: 10px;
            letter-spacing: .04em;
            margin-top: 1px;
        }

        .foto-card:hover .foto-add {
            opacity: 1;
        }
        .foto-add {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(202,255,0,.12);
            opacity: 0;
            transition: opacity .2s;
            pointer-events: none;
        }
        .foto-add span {
            background: var(--accent);
            color: #000;
            font-size: 11px;
            font-weight: 900;
            padding: 6px 14px;
            border-radius: 3px;
            letter-spacing: .06em;
            text-transform: uppercase;
        }

        /* ── LOADING / ERROR STATE ── */
        .state-msg {
            grid-column: 1 / -1;
            text-align: center;
            padding: 60px 0;
            color: var(--muted);
            font-size: 14px;
        }

        /* ── SKELETON ── */
        .skeleton {
            background: linear-gradient(90deg, var(--surface) 25%, #252525 50%, var(--surface) 75%);
            background-size: 200% 100%;
            animation: shimmer 1.2s infinite;
            aspect-ratio: 16/9;
            border-radius: 3px;
        }
        @keyframes shimmer { to { background-position: -200% 0; } }
    </style>
</head>
<body>

<!-- NAV -->
<nav>
    <a href="index.php">Home</a>
    <a href="foto_page.php" class="active">Foto Page</a>
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
        <a href="?dag=<?= $i ?>"
           class="<?= $i === $actieveDag ? 'active' : '' ?>">
            <?= htmlspecialchars($naam) ?>
        </a>
    <?php endforeach; ?>
</div>

<!-- STATUS BAR -->
<div class="status-bar" id="statusBar">
    <span class="status-dot"></span>
    <span id="fotoCount">–</span> foto's
    <span class="sep">&bull;</span>
    Bijgewerkt om <span id="updateTime">–</span>
</div>
<div class="progress-bar"><div class="progress-fill" id="progressFill"></div></div>

<!-- GRID -->
<div class="foto-grid" id="fotoGrid">
    <?php for ($i = 0; $i < 10; $i++): ?>
        <div class="skeleton"></div>
    <?php endfor; ?>
</div>

<!-- Hidden form for adding to cart -->
<form id="cartForm" method="POST" style="display:none">
    <input type="hidden" name="foto" id="cartFoto">
    <input type="hidden" name="id"   id="cartId">
</form>

<script>
    // ── Dutch time helper (always Europe/Amsterdam) ──
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
        return { h, m, s, label: `${h}:${m}:${s}` };
    }

    // ── Live Clock (Dutch time) ──
    function updateClock() {
        document.getElementById('liveClock').textContent = getDutchTime().label;
    }
    updateClock();
    setInterval(updateClock, 1000);

    // ── Fetch & render photos ──
    const DAG     = <?= $actieveDag ?>;
    const REFRESH = 10; // seconds between auto-refresh
    let refreshTimer;

    function renderFotos(fotos, currentTime) {
        const grid = document.getElementById('fotoGrid');
        grid.innerHTML = '';

        if (!fotos || fotos.length === 0) {
            grid.innerHTML = '<div class="state-msg">Geen foto\'s gevonden voor deze dag.</div>';
            return;
        }

        fotos.forEach((foto, index) => {
            const card = document.createElement('div');
            card.className = 'foto-card';
            card.title = `${foto.dag} · ID ${foto.id} · Klik om toe te voegen`;

            card.innerHTML = `
                <img src="${foto.pad}" alt="Foto ID ${foto.id}" loading="lazy">
                <div class="foto-overlay"></div>
                ${index === 0 ? '<div class="foto-badge-latest">Dichtsbij</div>' : ''}
                <div class="foto-badge-num">#${index + 1}</div>
                <div class="foto-meta">
                    <div class="foto-tijd">${foto.tijdLabel}</div>
                    <div class="foto-info">${foto.dag} &bull; ID ${foto.id}</div>
                </div>
                <div class="foto-add"><span>+ Winkelwagen</span></div>
            `;

            // Click → add to cart
            card.addEventListener('click', () => {
                document.getElementById('cartFoto').value = foto.pad;
                document.getElementById('cartId').value   = foto.id;
                document.getElementById('cartForm').submit();
            });

            grid.appendChild(card);
        });
    }

    function startProgressBar() {
        const fill = document.getElementById('progressFill');
        fill.style.transition = 'none';
        fill.style.width = '0%';
        // Force reflow
        fill.offsetWidth;
        fill.style.transition = `width ${REFRESH}s linear`;
        fill.style.width = '100%';
    }

    async function laadFotos() {
        try {
            const dutchTime = getDutchTime().label;
            const resp = await fetch(
                `backend/fotoController.php?dag=${DAG}&limit=10&time=${encodeURIComponent(dutchTime)}&_=${Date.now()}`
            );
            if (!resp.ok) throw new Error(`HTTP ${resp.status}`);
            const data = await resp.json();

            renderFotos(data.fotos, dutchTime);

            document.getElementById('fotoCount').textContent = data.totaal;
            document.getElementById('updateTime').textContent = dutchTime;

        } catch (err) {
            document.getElementById('fotoGrid').innerHTML =
                `<div class="state-msg">Fout bij laden: ${err.message}</div>`;
        }
    }

    function scheduleRefresh() {
        clearTimeout(refreshTimer);
        startProgressBar();
        refreshTimer = setTimeout(() => {
            laadFotos();
            scheduleRefresh();
        }, REFRESH * 1000);
    }

    // Initial load
    laadFotos().then(scheduleRefresh);
</script>
</body>
</html>

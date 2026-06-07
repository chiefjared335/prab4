<?php
session_start();

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$cartCount = array_sum(array_column($_SESSION['cart'], 'quantity'));

// Add to cart via POST (same system as foto_page.php)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['foto'])) {
    $foto = htmlspecialchars($_POST['foto']);
    $id   = htmlspecialchars($_POST['id'] ?? '');
    $key  = 'foto_' . $id;
    if (!isset($_SESSION['cart'][$key])) {
        $_SESSION['cart'][$key] = ['foto' => $foto, 'id' => $id, 'quantity' => 1, 'price' => 0.25];
    } else {
        $_SESSION['cart'][$key]['quantity']++;
    }
    $cartCount = array_sum(array_column($_SESSION['cart'], 'quantity'));
}

$dagNamen = ['Zondag','Maandag','Dinsdag','Woensdag','Donderdag','Vrijdag','Zaterdag'];
$huidigeDag = (int)date('w');
$actieveDag = $huidigeDag;
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FEED – Fotokiosk</title>
    <link href="https://fonts.googleapis.com/css2?family=Space+Mono:wght@400;700&family=Bebas+Neue&display=swap" rel="stylesheet">
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
            font-family: 'Space Mono', monospace;
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
            font-size: 11px;
            letter-spacing: .12em;
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
            font-size: 11px;
            color: var(--text);
            text-decoration: none;
            letter-spacing: .06em;
            text-transform: uppercase;
            font-family: 'Space Mono', monospace;
            transition: border-color .2s;
        }
        .cart-btn:hover { border-color: var(--accent); }
        .cart-badge {
            background: var(--accent);
            color: #000;
            border-radius: 50%;
            width: 20px; height: 20px;
            display: flex; align-items: center; justify-content: center;
            font-size: 11px; font-weight: 700;
        }

        /* ── HEADER ── */
        header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            padding: 24px 28px 0;
        }
        .logo {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 96px;
            font-weight: 400;
            line-height: 1;
            color: var(--accent);
            letter-spacing: .04em;
        }
        .clock-block { text-align: right; }
        #liveClock {
            font-family: 'Space Mono', monospace;
            font-size: 36px;
            font-weight: 700;
            color: var(--accent);
            letter-spacing: .04em;
            font-variant-numeric: tabular-nums;
        }
        .clock-meta {
            font-size: 10px;
            color: var(--muted);
            letter-spacing: .1em;
            text-transform: uppercase;
            margin-top: 6px;
        }

        /* ── DAG TABS ── */
        .dag-tabs {
            display: flex;
            gap: 6px;
            padding: 20px 28px 0;
            flex-wrap: wrap;
        }
        .dag-tabs a {
            padding: 8px 18px;
            border: 1px solid var(--border);
            border-radius: 4px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: var(--text);
            text-decoration: none;
            transition: background .15s, border-color .15s;
            font-family: 'Space Mono', monospace;
        }
        .dag-tabs a:hover { border-color: var(--accent); }
        .dag-tabs a.active {
            background: var(--accent);
            border-color: var(--accent);
            color: #000;
        }

        /* ── TITLE ROW ── */
        .section-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 24px 28px 12px;
        }
        .section-title {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 32px;
            color: var(--text);
            letter-spacing: .08em;
        }
        .section-meta {
            display: flex;
            align-items: center;
            gap: 16px;
        }
        .prev-btn {
            font-size: 11px;
            color: var(--muted);
            text-decoration: none;
            letter-spacing: .06em;
            text-transform: uppercase;
            border-bottom: 1px solid var(--border);
            padding-bottom: 2px;
            transition: color .2s, border-color .2s;
            font-family: 'Space Mono', monospace;
            cursor: pointer;
            background: none;
            border-top: none;
            border-left: none;
            border-right: none;
        }
        .prev-btn:hover { color: var(--accent); border-bottom-color: var(--accent); }

        /* ── STATUS ── */
        .status-bar {
            padding: 0 28px 8px;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 11px;
            color: var(--muted);
        }
        .status-dot {
            width: 8px; height: 8px;
            border-radius: 50%;
            background: var(--accent);
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0%,100% { opacity:1; }
            50% { opacity:.4; }
        }
        .status-bar .sep { color: var(--border); }
        .progress-bar {
            height: 2px;
            background: var(--border);
            margin: 0 28px 20px;
            border-radius: 1px;
            overflow: hidden;
        }
        .progress-fill {
            height: 100%;
            width: 0%;
            background: var(--accent);
            transition: width linear;
        }

        /* ── 2×2 FOTO GRID ── */
        .foto-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 8px;
            padding: 0 28px 28px;
        }

        .foto-card {
            position: relative;
            aspect-ratio: 4/3;
            overflow: hidden;
            border-radius: 4px;
            cursor: pointer;
            background: var(--surface);
            border: 1px solid var(--border);
            transition: border-color .2s;
        }
        .foto-card:hover { border-color: var(--accent); }
        .foto-card img {
            width: 100%; height: 100%;
            object-fit: cover;
            display: block;
            transition: transform .3s;
        }
        .foto-card:hover img { transform: scale(1.05); }

        .foto-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(to top, rgba(0,0,0,.75) 0%, transparent 50%);
            pointer-events: none;
        }

        .foto-badge-num {
            position: absolute;
            top: 10px; right: 10px;
            background: rgba(0,0,0,.6);
            color: var(--muted);
            font-size: 10px;
            font-weight: 700;
            padding: 3px 7px;
            border-radius: 2px;
            letter-spacing: .06em;
            font-family: 'Space Mono', monospace;
        }

        .foto-meta {
            position: absolute;
            bottom: 10px; left: 12px;
        }
        .foto-tijd {
            color: var(--accent);
            font-family: 'Bebas Neue', sans-serif;
            font-size: 26px;
            letter-spacing: .06em;
        }
        .foto-info {
            color: var(--muted);
            font-size: 10px;
            letter-spacing: .04em;
            margin-top: 2px;
            font-family: 'Space Mono', monospace;
        }

        .foto-add-overlay {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(202,255,0,.1);
            opacity: 0;
            transition: opacity .2s;
            pointer-events: none;
        }
        .foto-card:hover .foto-add-overlay { opacity: 1; }
        .foto-add-overlay span {
            background: var(--accent);
            color: #000;
            font-size: 11px;
            font-weight: 700;
            padding: 8px 18px;
            border-radius: 3px;
            letter-spacing: .08em;
            text-transform: uppercase;
            font-family: 'Space Mono', monospace;
        }

        /* ── SKELETON ── */
        .skeleton {
            background: linear-gradient(90deg, var(--surface) 25%, #252525 50%, var(--surface) 75%);
            background-size: 200% 100%;
            animation: shimmer 1.2s infinite;
            aspect-ratio: 4/3;
            border-radius: 4px;
        }
        @keyframes shimmer { to { background-position: -200% 0; } }

        .state-msg {
            grid-column: 1 / -1;
            text-align: center;
            padding: 60px 0;
            color: var(--muted);
            font-size: 13px;
        }

        /* ── RESPONSIVE ── */
        @media(max-width:600px) {
            .logo { font-size: 60px; }
            header { flex-direction: column; gap: 12px; }
            .clock-block { text-align: left; }
            .dag-tabs { gap: 4px; }
            .dag-tabs a { padding: 6px 12px; font-size: 10px; }
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
        <span class="cart-badge" id="cartBadge"><?= $cartCount ?></span>
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
        <a href="#" class="dagtab <?= $i === $actieveDag ? 'active' : '' ?>"
           data-dag="<?= $i ?>"><?= htmlspecialchars($naam) ?></a>
    <?php endforeach; ?>
</div>

<!-- SECTION HEADER -->
<div class="section-header">
    <div class="section-title" id="sectionTitle"><?= htmlspecialchars($dagNamen[$actieveDag]) ?></div>
    <div class="section-meta">
        <button class="prev-btn" id="prevBtn">← Vorige 4 foto's</button>
    </div>
</div>

<!-- STATUS BAR -->
<div class="status-bar">
    <span class="status-dot"></span>
    <span id="fotoCount">–</span> foto's
    <span class="sep">&bull;</span>
    Bijgewerkt om <span id="updateTime">–</span>
    <span class="sep">&bull;</span>
    Refresh in <span id="countdown">10</span>s
</div>
<div class="progress-bar"><div class="progress-fill" id="progressFill"></div></div>

<!-- 2×2 GRID -->
<div class="foto-grid" id="fotoGrid">
    <div class="skeleton"></div>
    <div class="skeleton"></div>
    <div class="skeleton"></div>
    <div class="skeleton"></div>
</div>

<!-- Hidden form for cart -->
<form id="cartForm" method="POST" style="display:none">
    <input type="hidden" name="foto" id="cartFoto">
    <input type="hidden" name="id"   id="cartId">
</form>

<script>
    const REFRESH = 10;
    let currentDag = <?= $actieveDag ?>;
    let offset = 0;
    let totalFotos = 0;
    let refreshTimer;
    let countdownInterval;
    let countdownVal = REFRESH;

    // Dutch time
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

    // Live clock
    function updateClock() {
        document.getElementById('liveClock').textContent = getDutchTime();
    }
    updateClock();
    setInterval(updateClock, 1000);

    // Day tabs
    document.querySelectorAll('.dagtab').forEach(tab => {
        tab.addEventListener('click', e => {
            e.preventDefault();
            const dag = parseInt(tab.dataset.dag);
            currentDag = dag;
            offset = 0;
            document.querySelectorAll('.dagtab').forEach(t => t.classList.remove('active'));
            tab.classList.add('active');
            const dagNamen = ['Zondag','Maandag','Dinsdag','Woensdag','Donderdag','Vrijdag','Zaterdag'];
            document.getElementById('sectionTitle').textContent = dagNamen[dag];
            laadFotos();
            resetRefresh();
        });
    });

    // Prev button
    document.getElementById('prevBtn').addEventListener('click', () => {
        if (offset + 4 < totalFotos) {
            offset += 4;
            laadFotos(false);
        }
    });

    function renderFotos(fotos) {
        const grid = document.getElementById('fotoGrid');
        grid.innerHTML = '';

        if (!fotos || fotos.length === 0) {
            grid.innerHTML = '<div class="state-msg">Geen foto\'s gevonden voor deze dag.</div>';
            return;
        }

        // Show max 4
        fotos.slice(0, 4).forEach((foto, index) => {
            const card = document.createElement('div');
            card.className = 'foto-card';
            card.innerHTML = `
                <img src="${foto.pad}" alt="Foto ${foto.id}" loading="lazy">
                <div class="foto-overlay"></div>
                <div class="foto-badge-num">#${offset + index + 1}</div>
                <div class="foto-meta">
                    <div class="foto-tijd">${foto.tijdLabel}</div>
                    <div class="foto-info">${foto.dag} &bull; ID ${foto.id}</div>
                </div>
                <div class="foto-add-overlay"><span>+ Winkelwagen</span></div>
            `;
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
        fill.offsetWidth;
        fill.style.transition = `width ${REFRESH}s linear`;
        fill.style.width = '100%';
    }

    function resetRefresh() {
        clearTimeout(refreshTimer);
        clearInterval(countdownInterval);
        countdownVal = REFRESH;
        document.getElementById('countdown').textContent = countdownVal;
        startProgressBar();
        scheduleRefresh();
    }

    function scheduleRefresh() {
        countdownInterval = setInterval(() => {
            countdownVal--;
            document.getElementById('countdown').textContent = countdownVal;
            if (countdownVal <= 0) {
                clearInterval(countdownInterval);
            }
        }, 1000);

        refreshTimer = setTimeout(() => {
            offset = 0; // reset to latest on auto-refresh
            laadFotos();
            resetRefresh();
        }, REFRESH * 1000);
    }

    async function laadFotos(autoRefresh = true) {
        const dutchTime = getDutchTime();
        try {
            const resp = await fetch(
                `backend/fotoController.php?dag=${currentDag}&limit=4&offset=${offset}&time=${encodeURIComponent(dutchTime)}&_=${Date.now()}`
            );
            if (!resp.ok) throw new Error(`HTTP ${resp.status}`);
            const data = await resp.json();

            totalFotos = data.totaal;
            renderFotos(data.fotos);

            document.getElementById('fotoCount').textContent = data.totaal;
            document.getElementById('updateTime').textContent = dutchTime;

            // Update prev button state
            const prevBtn = document.getElementById('prevBtn');
            prevBtn.style.opacity = (offset + 4 < totalFotos) ? '1' : '0.3';

        } catch (err) {
            document.getElementById('fotoGrid').innerHTML =
                `<div class="state-msg">Fout bij laden: ${err.message}</div>`;
        }
    }

    // Initial load
    laadFotos();
    resetRefresh();
</script>
</body>
</html>
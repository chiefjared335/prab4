<?php
session_start();

// Winkelwagen aanmaken als hij nog niet bestaat
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Product toevoegen
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name'])) {
    $name = $_POST['name'];
    $price = $_POST['price'];

    if (!isset($_SESSION['cart'][$name])) {
        $_SESSION['cart'][$name] = [
            'price' => $price,
            'quantity' => 1
        ];
    } else {
        $_SESSION['cart'][$name]['quantity']++;
    }
}

// Winkelwagen leegmaken
if (isset($_GET['empty'])) {
    unset($_SESSION['cart']);
    header("Location: winkelwagen.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Fotokiosk Winkelwagen</title>
<?php
session_start();

if (isset($_GET['leeg'])) {
    $_SESSION['winkelwagen'] = [];
}
?>
</head>
<body>

    <nav>
        <div>
            <a href="index.php">Home</a>
            <a href="foto_page.php">Foto's</a>
        </div>
        <a href="winkelwagen.php" class="winkelwagen-link">🛒 Winkelwagen</a>
    </nav>

    <h1>Winkelwagen</h1>
    h3>Hier zie je de producten die je aan je winkelmand hebt toegevoegd.</h3>

    <?php
    if (empty($_SESSION['winkelwagen'])) {
        echo "<p>Je winkelwagen is leeg.</p>";
    } else {
        $totaal = 0;

        echo "<table border='1'>";
        echo "<tr><th>Product</th><th>Prijs</th></tr>";

        foreach ($_SESSION['winkelwagen'] as $item) {
            echo "<tr>";
            echo "<td>" . $item['naam'] . "</td>";
            echo "<td>€" . number_format($item['prijs'], 2, ',', '.') . "</td>";
            echo "</tr>";

            $totaal += $item['prijs'];
        }

        echo "</table>";
        echo "<p><strong>Totaal: €" . number_format($totaal, 2, ',', '.') . "</strong></p>";
        echo "<a href='winkelwagen.php?leeg=1'>Winkelwagen leegmaken</a>";
    }
    ?>

    <br><br>
    <a href="foto_page.php">Terug naar foto's</a>

</body>
</html>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #0d0d0d;
            color: #e6e6e6;
            margin: 0;
            padding: 20px;
        }

        h1 {
            text-align: center;
            color: white;
        }

        /* Sidebar winkelwagen */
        .winkelwagen-sidebar {
            position: fixed;
            right: 20px;
            top: 20px;
            width: 250px;
            background: #1a1a1a;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 0 20px rgba(0,0,0,0.6);
            border: 1px solid #333;
        }

        .winkelwagen-sidebar h3 {
            margin-top: 0;
            text-align: center;
            color: #fff;
        }

        .sidebar-btn {
            display: block;
            margin-top: 15px;
            padding: 10px;
            background: #3a7afe;
            color: white;
            text-align: center;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
        }

        .sidebar-btn:hover {
            background: #1f5edb;
        }

        /* Producten */
        .products {
            display: flex;
            gap: 30px;
            margin-top: 40px;
        }

        .product {
            background: #1a1a1a;
            padding: 15px;
            border-radius: 10px;
            width: 180px;
            text-align: center;
            border: 1px solid #333;
        }

        .product img {
            width: 100%;
            border-radius: 8px;
        }

        .product button {
            margin-top: 10px;
            padding: 8px 12px;
            background: #3a7afe;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .product button:hover {
            background: #1f5edb;
        }
    </style>
</head>
<body>

<h1>📸 Fotokiosk</h1>

<!-- PRODUCTEN -->
<div class="products">

    <div class="product">
        <img src="foto1.jpg">
        <p><strong>Foto 10x15</strong></p>
        <p>€0.25</p>
        <form method="POST">
            <input type="hidden" name="name" value="Foto 10x15">
            <input type="hidden" name="price" value="0.25">
            <button type="submit">Toevoegen</button>
        </form>
    </div>

    <div class="product">
        <img src="foto2.jpg">
        <p><strong>Foto 20x30</strong></p>
        <p>€1.00</p>
        <form method="POST">
            <input type="hidden" name="name" value="Foto 20x30">
            <input type="hidden" name="price" value="1.00">
            <button type="submit">Toevoegen</button>
        </form>
    </div>

</div>

<!-- SIDEBAR WINKELWAGEN -->
<div class="winkelwagen-sidebar">
    <h3>🛒 Winkelwagen</h3>

    <?php
    if (!empty($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $name => $item) {
            echo "<p><strong>$name</strong> x {$item['quantity']}</p>";
        }
        echo "<a href='?empty=1' class='sidebar-btn' style='background:#d9534f;'>Leegmaken</a>";
    } else {
        echo "<p>Winkelwagen is leeg</p>";
    }
    ?>

    <a href="winkelwagen.php" class="sidebar-btn">Volledige winkelwagen</a>
</div>
<div style="text-align:center; margin-top:20px;">
    <a href="foto_page.php" style="display:inline-block; padding:10px 20px; background:#3a7afe; color:white; text-decoration:none; border-radius:5px; font-weight:bold;">← Terug naar Foto's</a>
</div>
</body>
</html>

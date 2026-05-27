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

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #121212;
            color: #e6e6e6;
            margin: 0;
            padding: 20px;
        }

        h1, h2 {
            text-align: center;
            color: #ffffff;
        }

        .products {
            display: flex;
            justify-content: center;
            gap: 30px;
            margin-bottom: 40px;
        }

        .product {
            background: #1e1e1e;
            border-radius: 10px;
            padding: 15px;
            width: 180px;
            text-align: center;
            box-shadow: 0 0 10px #000;
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

        .cart-box {
            background: #1e1e1e;
            padding: 20px;
            width: 400px;
            margin: 0 auto;
            border-radius: 10px;
            box-shadow: 0 0 10px #000;
            border: 1px solid #333;
        }

        .cart-item {
            padding: 8px 0;
            border-bottom: 1px solid #333;
        }

        .empty-btn {
            display: inline-block;
            margin-top: 15px;
            padding: 10px 15px;
            background: #d9534f;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }

        .empty-btn:hover {
            background: #b52b27;
        }
    </style>
</head>
<body>

<h1>📸 Fotokiosk</h1>
<h2>Kies je fotoformaat</h2>

<div class="products">

    <!-- Product 1 -->
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

    <!-- Product 2 -->
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

<h2>🛒 Winkelwagen</h2>

<div class="cart-box">

<?php
$total = 0;

if (!empty($_SESSION['cart'])) {

    foreach ($_SESSION['cart'] as $name => $item) {
        $lineTotal = $item['price'] * $item['quantity'];
        $total += $lineTotal;

        echo "<div class='cart-item'>
                <strong>$name</strong><br>
                €{$item['price']} x {$item['quantity']} = <strong>€$lineTotal</strong>
              </div>";
    }

    echo "<h3>Totaal: €$total</h3>";
    echo "<a class='empty-btn' href='?empty=1'>Winkelwagen leegmaken</a>";

} else {
    echo "<p>Winkelwagen is leeg</p>";
}
?>

</div>
<div style="text-align:center; margin-top:20px;">
    <a href="foto_page.php" style="display:inline-block; padding:10px 20px; background:#3a7afe; color:white; text-decoration:none; border-radius:5px; font-weight:bold;">← Terug naar Foto's</a>
</div>
</body>
</html>


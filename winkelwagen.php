<?php
session_start();

/*
|--------------------------------------------------------------------------
| Winkelwagen initialiseren
|--------------------------------------------------------------------------
*/
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

/*
|--------------------------------------------------------------------------
| Product toevoegen
|--------------------------------------------------------------------------
*/
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name'])) {

    $name = htmlspecialchars($_POST['name']);
    $price = floatval($_POST['price']);

    if (!isset($_SESSION['cart'][$name])) {

        $_SESSION['cart'][$name] = [
            'price' => $price,
            'quantity' => 1
        ];

    } else {

        $_SESSION['cart'][$name]['quantity']++;
    }
}

/*
| Winkelwagen leegmaken
|--------------------------------------------------------------------------
*/
if (isset($_GET['empty'])) {

    $_SESSION['cart'] = [];

    header("Location: winkelwagen.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fotokiosk Winkelwagen</title>

    <style>

        *{
            margin:0;
            padding:0;
            box-sizing:border-box;
        }

        body{
            font-family: Arial, sans-serif;
            background:#0f0f0f;
            color:white;
        }

        /*
        |--------------------------------------------------------------------------
        | Navbar
        |--------------------------------------------------------------------------
        */

        nav{
            display:flex;
            justify-content:space-between;
            align-items:center;
            padding:20px 40px;
            background:#161616;
            border-bottom:1px solid #2a2a2a;
        }

        nav .links{
            display:flex;
            gap:20px;
        }

        nav a{
            color:white;
            text-decoration:none;
            font-weight:bold;
            transition:0.3s;
        }

        nav a:hover{
            color:#4a8cff;
        }

        /*
        |--------------------------------------------------------------------------
        | Container
        |--------------------------------------------------------------------------
        */

        .container{
            max-width:1200px;
            margin:auto;
            padding:40px 20px;
        }

        h1{
            font-size:42px;
            margin-bottom:10px;
        }

        .subtitle{
            color:#b5b5b5;
            margin-bottom:40px;
        }

        /*
        |--------------------------------------------------------------------------
        | Producten
        |--------------------------------------------------------------------------
        */

        .products{
            display:grid;
            grid-template-columns:repeat(auto-fit, minmax(250px, 1fr));
            gap:25px;
            margin-bottom:50px;
        }

        .product{
            background:#1a1a1a;
            border-radius:18px;
            overflow:hidden;
            border:1px solid #2f2f2f;
            transition:0.3s;
        }

        .product:hover{
            transform:translateY(-5px);
            box-shadow:0 10px 30px rgba(0,0,0,0.5);
        }

        .product img{
            width:100%;
            height:220px;
            object-fit:cover;
        }

        .product-content{
            padding:20px;
        }

        .product h3{
            margin-bottom:10px;
        }

        .price{
            color:#4a8cff;
            font-size:22px;
            font-weight:bold;
            margin-bottom:15px;
        }

        /*
        |--------------------------------------------------------------------------
        | Buttons
        |--------------------------------------------------------------------------
        */

        button,
        .btn{
            background:#4a8cff;
            color:white;
            border:none;
            padding:12px 18px;
            border-radius:10px;
            cursor:pointer;
            font-weight:bold;
            text-decoration:none;
            display:inline-block;
            transition:0.3s;
        }

        button:hover,
        .btn:hover{
            background:#2f6de0;
        }

        .btn-danger{
            background:#d9534f;
        }

        .btn-danger:hover{
            background:#c9302c;
        }

        /*
        |--------------------------------------------------------------------------
        | Winkelwagen
        |--------------------------------------------------------------------------
        */

        .cart{
            background:#161616;
            border-radius:18px;
            padding:30px;
            border:1px solid #2a2a2a;
        }

        table{
            width:100%;
            border-collapse:collapse;
            margin-top:20px;
        }

        table th{
            text-align:left;
            padding:15px;
            background:#202020;
        }

        table td{
            padding:15px;
            border-bottom:1px solid #2f2f2f;
        }

        .total{
            margin-top:25px;
            text-align:right;
            font-size:24px;
            font-weight:bold;
            color:#4a8cff;
        }

        .cart-actions{
            margin-top:25px;
            display:flex;
            gap:15px;
        }

        /*
        |--------------------------------------------------------------------------
        | Empty cart
        |--------------------------------------------------------------------------
        */

        .empty{
            padding:30px;
            text-align:center;
            color:#b5b5b5;
        }

    </style>
</head>
<body>

<!-- NAVBAR -->
<nav>

    <div class="links">
        <a href="index.php">Home</a>
        <a href="foto_page.php">Foto's</a>
    </div>

    <a href="winkelwagen.php">🛒 Winkelwagen</a>

</nav>

<div class="container">

    <h1>📸 Fotokiosk</h1>

    <p class="subtitle">
        Professionele fotoprints bestellen in hoge kwaliteit.
    </p>

    <!-- PRODUCTEN -->
    <div class="products">

        <!-- Product 1 -->
        <div class="product">

            <img src="foto1.jpg" alt="Foto 10x15">

            <div class="product-content">

                <h3>Foto 10x15</h3>

                <div class="price">€0,25</div>

                <form method="POST">

                    <input type="hidden" name="name" value="Foto 10x15">
                    <input type="hidden" name="price" value="0.25">

                    <button type="submit">
                        Toevoegen aan winkelwagen
                    </button>

                </form>

            </div>

        </div>

        <!-- Product 2 -->
        <div class="product">

            <img src="foto2.jpg" alt="Foto 20x30">

            <div class="product-content">

                <h3>Foto 20x30</h3>

                <div class="price">€1,00</div>

                <form method="POST">

                    <input type="hidden" name="name" value="Foto 20x30">
                    <input type="hidden" name="price" value="1.00">

                    <button type="submit">
                        Toevoegen aan winkelwagen
                    </button>

                </form>

            </div>

        </div>

    </div>

    <!-- WINKELWAGEN -->
    <div class="cart">

        <h2>🛒 Winkelwagen</h2>

        <?php if (empty($_SESSION['cart'])): ?>

            <div class="empty">
                Je winkelwagen is momenteel leeg.
            </div>

        <?php else: ?>

            <table>

                <tr>
                    <th>Product</th>
                    <th>Prijs</th>
                    <th>Aantal</th>
                    <th>Subtotaal</th>
                </tr>

                <?php
                    $total = 0;

                    foreach ($_SESSION['cart'] as $name => $item):

                    $subtotal = $item['price'] * $item['quantity'];

                    $total += $subtotal;
                ?>

                <tr>

                    <td><?= $name ?></td>

                    <td>
                        €<?= number_format($item['price'], 2, ',', '.') ?>
                    </td>

                    <td>
                        <?= $item['quantity'] ?>
                    </td>

                    <td>
                        €<?= number_format($subtotal, 2, ',', '.') ?>
                    </td>

                </tr>

                <?php endforeach; ?>

            </table>

            <div class="total">
                Totaal: €<?= number_format($total, 2, ',', '.') ?>
            </div>

            <div class="cart-actions">

                <a href="?empty=1" class="btn btn-danger">
                    Winkelwagen leegmaken
                </a>

                <a href="checkout.php" class="btn">
                    Afrekenen
                </a>

            </div>

        <?php endif; ?>

    </div>

</div>

</body>
</html>
```

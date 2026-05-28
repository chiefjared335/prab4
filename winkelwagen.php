```css
/* =========================================
   RESET
========================================= */

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
}

html{
    scroll-behavior:smooth;
}

body{
    font-family:'Segoe UI', sans-serif;
    background:
        radial-gradient(circle at top left, #1d3c73 0%, transparent 25%),
        radial-gradient(circle at bottom right, #121212 0%, #050505 60%);
    color:white;
    min-height:100vh;
    overflow-x:hidden;
}

/* =========================================
   NAVBAR
========================================= */

nav{
    width:100%;
    display:flex;
    justify-content:space-between;
    align-items:center;
    padding:24px 60px;
    position:sticky;
    top:0;
    z-index:1000;

    background:rgba(10,10,10,0.65);
    backdrop-filter:blur(18px);

    border-bottom:1px solid rgba(255,255,255,0.08);

    box-shadow:
        0 10px 30px rgba(0,0,0,0.35);
}

nav .logo{
    font-size:28px;
    font-weight:700;
    letter-spacing:1px;
}

nav .links{
    display:flex;
    gap:30px;
}

nav a{
    color:white;
    text-decoration:none;
    font-weight:600;
    position:relative;
    transition:0.3s ease;
}

nav a::after{
    content:'';
    position:absolute;
    left:0;
    bottom:-8px;
    width:0%;
    height:2px;
    background:#4a8cff;
    transition:0.3s;
}

nav a:hover{
    color:#6aa2ff;
}

nav a:hover::after{
    width:100%;
}

/* =========================================
   MAIN CONTAINER
========================================= */

.container{
    width:100%;
    max-width:1400px;
    margin:auto;
    padding:60px 40px;
}

/* =========================================
   HERO SECTION
========================================= */

.hero{
    text-align:center;
    margin-bottom:70px;
}

.hero h1{
    font-size:72px;
    font-weight:800;
    line-height:1.1;

    background:linear-gradient(to right, #ffffff, #73a9ff);
    -webkit-background-clip:text;
    -webkit-text-fill-color:transparent;

    margin-bottom:20px;
}

.hero p{
    font-size:20px;
    color:#b8b8b8;
    max-width:700px;
    margin:auto;
    line-height:1.7;
}

/* =========================================
   PRODUCTS GRID
========================================= */

.products{
    display:grid;
    grid-template-columns:repeat(auto-fit, minmax(320px, 1fr));
    gap:35px;

    margin-bottom:80px;
}

/* =========================================
   PRODUCT CARD
========================================= */

.product{
    position:relative;

    background:rgba(18,18,18,0.85);

    border:1px solid rgba(255,255,255,0.08);

    border-radius:28px;

    overflow:hidden;

    transition:0.45s ease;

    backdrop-filter:blur(12px);

    box-shadow:
        0 10px 35px rgba(0,0,0,0.35);
}

.product:hover{
    transform:
        translateY(-10px)
        scale(1.02);

    border-color:rgba(74,140,255,0.5);

    box-shadow:
        0 20px 60px rgba(0,0,0,0.5),
        0 0 30px rgba(74,140,255,0.15);
}

.product::before{
    content:'';
    position:absolute;
    inset:0;

    background:
        linear-gradient(
            145deg,
            rgba(255,255,255,0.05),
            transparent
        );

    pointer-events:none;
}

.product img{
    width:100%;
    height:280px;
    object-fit:cover;

    transition:0.5s;
}

.product:hover img{
    transform:scale(1.05);
}

.product-content{
    padding:28px;
}

.product h3{
    font-size:28px;
    margin-bottom:14px;
    font-weight:700;
}

.product-description{
    color:#b6b6b6;
    line-height:1.6;
    margin-bottom:22px;
}

.price{
    font-size:34px;
    font-weight:800;

    color:#6ea8ff;

    margin-bottom:24px;
}

/* =========================================
   BUTTONS
========================================= */

button,
.btn{
    width:100%;

    padding:16px;

    border:none;
    border-radius:16px;

    background:
        linear-gradient(
            135deg,
            #4a8cff,
            #2563eb
        );

    color:white;

    font-size:16px;
    font-weight:700;

    cursor:pointer;

    transition:0.35s ease;

    box-shadow:
        0 10px 25px rgba(37,99,235,0.35);

    text-decoration:none;

    display:flex;
    align-items:center;
    justify-content:center;

    gap:10px;
}

button:hover,
.btn:hover{
    transform:translateY(-3px);

    box-shadow:
        0 15px 35px rgba(37,99,235,0.45);
}

.btn-danger{
    background:
        linear-gradient(
            135deg,
            #ff4b5c,
            #d90429
        );

    box-shadow:
        0 10px 25px rgba(217,4,41,0.35);
}

.btn-danger:hover{
    box-shadow:
        0 15px 35px rgba(217,4,41,0.45);
}

/* =========================================
   CART SECTION
========================================= */

.cart{
    background:rgba(15,15,15,0.8);

    border:1px solid rgba(255,255,255,0.08);

    border-radius:30px;

    padding:40px;

    backdrop-filter:blur(15px);

    box-shadow:
        0 15px 50px rgba(0,0,0,0.4);
}

.cart h2{
    font-size:36px;
    margin-bottom:30px;
}

/* =========================================
   TABLE
========================================= */

table{
    width:100%;
    border-collapse:collapse;
    overflow:hidden;
    border-radius:20px;
}

table th{
    background:rgba(255,255,255,0.05);

    padding:20px;

    text-align:left;

    font-size:15px;
    letter-spacing:1px;
    text-transform:uppercase;

    color:#9cbcff;
}

table td{
    padding:22px 20px;

    border-bottom:
        1px solid rgba(255,255,255,0.05);

    font-size:16px;
}

table tr{
    transition:0.25s;
}

table tr:hover{
    background:rgba(255,255,255,0.03);
}

/* =========================================
   TOTAL
========================================= */

.total{
    margin-top:35px;

    display:flex;
    justify-content:flex-end;
    align-items:center;

    font-size:34px;
    font-weight:800;

    color:#6ea8ff;
}

/* =========================================
   CART BUTTONS
========================================= */

.cart-actions{
    margin-top:35px;

    display:flex;
    gap:20px;
}

/* =========================================
   EMPTY CART
========================================= */

.empty{
    padding:60px;
    text-align:center;

    background:rgba(255,255,255,0.03);

    border-radius:20px;

    color:#b6b6b6;

    font-size:18px;
}

/* =========================================
   SCROLLBAR
========================================= */

::-webkit-scrollbar{
    width:10px;
}

::-webkit-scrollbar-track{
    background:#111;
}

::-webkit-scrollbar-thumb{
    background:#3b82f6;
    border-radius:20px;
}

/* =========================================
   RESPONSIVE
========================================= */

@media(max-width:900px){

    nav{
        padding:20px;
        flex-direction:column;
        gap:20px;
    }

    .hero h1{
        font-size:48px;
    }

    .container{
        padding:40px 20px;
    }

    .cart-actions{
        flex-direction:column;
    }

    table{
        display:block;
        overflow-x:auto;
    }
}

@media(max-width:600px){

    .hero h1{
        font-size:38px;
    }

    .hero p{
        font-size:16px;
    }

    .product h3{
        font-size:22px;
    }

    .price{
        font-size:28px;
    }

    .cart{
        padding:25px;
    }
}
```

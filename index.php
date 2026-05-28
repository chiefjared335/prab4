<?php
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
?>

<!DOCTYPE html>
<html lang="nl">
<head>
<meta charset="UTF-8">
<title>Fotokiosk</title>

<style>
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
}

body{
    font-family:'Poppins',sans-serif;
    min-height:100vh;

    background:
        radial-gradient(circle at top left,#2563eb 0%,transparent 25%),
        radial-gradient(circle at bottom right,#7c3aed 0%,transparent 25%),
        linear-gradient(135deg,#0f172a,#111827,#020617);

    color:white;

    display:flex;
    flex-direction:column;
    align-items:center;

    padding:30px;
}

/* TOP BAR */

.topbar{
    width:100%;
    max-width:1500px;

    display:flex;
    align-items:center;
    justify-content:center;

    position:relative;

    margin-bottom:40px;
}

/* TITEL + TIMER */

.center-info{
    display:flex;
    align-items:center;
    gap:20px;
}

.title{
    font-size:52px;
    font-weight:700;

    background:linear-gradient(to right,#ffffff,#60a5fa);

    -webkit-background-clip:text;
    -webkit-text-fill-color:transparent;

    letter-spacing:2px;
}

/* TIMER */

.timer{
    background:rgba(255,255,255,0.08);

    border:1px solid rgba(255,255,255,0.08);

    backdrop-filter:blur(12px);

    padding:12px 22px;

    border-radius:18px;

    font-size:20px;
    font-weight:600;

    box-shadow:0 10px 30px rgba(0,0,0,0.25);
}

/* WINKELWAGEN */

.cart-button{
    position:absolute;
    right:0;

    width:60px;
    height:60px;

    border-radius:18px;

    border:1px solid rgba(255,255,255,0.08);

    background:rgba(255,255,255,0.08);

    backdrop-filter:blur(12px);

    display:flex;
    align-items:center;
    justify-content:center;

    cursor:pointer;

    transition:0.3s;

    box-shadow:0 10px 30px rgba(0,0,0,0.25);
}

.cart-button:hover{
    transform:translateY(-4px) scale(1.05);

    background:rgba(255,255,255,0.15);
}

.cart-button i{
    font-size:28px;
    color:white;
}

/* FOTO GRID */

.grid{
    width:100%;
    max-width:1500px;

    display:grid;

    grid-template-columns:repeat(2,1fr);

    gap:28px;
}

/* FOTO CARD */

.photo-card{
    position:relative;

    background:rgba(255,255,255,0.08);

    border-radius:26px;

    overflow:hidden;

    border:1px solid rgba(255,255,255,0.08);

    backdrop-filter:blur(12px);

    box-shadow:
        0 15px 35px rgba(0,0,0,0.35),
        inset 0 0 0 1px rgba(255,255,255,0.03);

    transition:0.35s;
}

.photo-card:hover{
    transform:translateY(-6px) scale(1.01);
}

.photo-card img{
    width:100%;
    height:360px;

    object-fit:cover;

    display:block;

    transition:1s;
}

.photo-card:hover img{
    transform:scale(1.04);
}

/* OVERLAY */

.overlay{
    position:absolute;

    left:0;
    right:0;
    bottom:0;

    padding:18px;

    background:linear-gradient(
        to top,
        rgba(0,0,0,0.78),
        transparent
    );
}

.overlay h3{
    font-size:20px;
    font-weight:600;
}

/* RESPONSIVE */

@media(max-width:1000px){

    .grid{
        grid-template-columns:1fr;
    }

    .topbar{
        flex-direction:column;
        gap:20px;
    }

    .cart-button{
        position:relative;
    }

    .title{
        font-size:38px;
    }
}
</style>
</head>

<body>

<h1>Fotokiosk</h1>

<button class="daybtn" onclick="setDay(0,this)">Zondag</button>
<button class="daybtn" onclick="setDay(1,this)">Maandag</button>
<button class="daybtn" onclick="setDay(2,this)">Dinsdag</button>
<button class="daybtn" onclick="setDay(3,this)">Woensdag</button>
<button class="daybtn" onclick="setDay(4,this)">Donderdag</button>
<button class="daybtn" onclick="setDay(5,this)">Vrijdag</button>
<button class="daybtn" onclick="setDay(6,this)">Zaterdag</button>

<!-- HUIDIGE DAG -->
<div class="current-day">
: <span id="currentDayText"></span>
</div>


<div class="timer">
 <span id="count">10</span> sec
</div>

<div class="grid">
  <div class="box"><img id="img0"></div>
  <div class="box"><img id="img1"></div>
  <div class="box"><img id="img2"></div>
  <div class="box"><img id="img3"></div>
  <div class="box"><img id="img4"></div>
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

setDay(selectedDay, document.querySelectorAll(".daybtn")[selectedDay]);


function setDay(day, button){

    // gekozen dag opslaan
    selectedDay = day;

    // reset slideshow index
    index = 0;

    // actieve knop styling
    document.querySelectorAll(".daybtn")
    .forEach(btn => btn.classList.remove("active"));

    button.classList.add("active");

    // update foto's
    updatePhotos();
}


function updatePhotos(){

    // alleen foto's van gekozen dag
    const folder = folders[selectedDay];

    // lijst van gekozen dag
    const list = data[folder];

    // check of foto's bestaan
    if(!list || list.length === 0) return;

    // vul 4 blokken
    for(let i = 0; i < 4; i++){

        // volgende foto pakken
        const imgIndex = (index + i) % list.length;

        // foto tonen
        document.getElementById("img" + i).src =
            list[imgIndex];
    }
}

function tick() {
  time--;
  document.getElementById("count").innerText = time;

  if (time <= 0) {
    index++;
    updatePhotos();
    time = 10;
  }
}

updatePhotos();
setInterval(tick, 1000);
</script>

</body>
</html>
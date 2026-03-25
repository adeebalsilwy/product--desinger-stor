<!DOCTYPE html>
<html lang="ar">

<head>
    <meta charset="utf-8">
    <title>تصميم صفين مختلفين + روابط</title>
    <style>
        body {
            background-color: #e0e5ec;
            font-family: 'Nunito Sans', sans-serif;
            margin: 0;
            padding: 0;
        }

        .titel {
            text-align: center;
            font-size: 28px;
            color: white;
            margin: 30px 0 10px;
        }

        .flexbox {
            width: 800px;
            height: 420px;
            display: -webkit-box;
            display: box;
            -webkit-box-orient: horizontal;
            box-orient: horizontal;
            margin: 0 auto;

        }

        .flexbox > div {
            -webkit-transition: 1s ease-out;
            transition: 1s ease-out;
            -webkit-border-radius: 10px;
            border-radius: 10px;
            border: 2px solid black;
            width: 120px;
            margin: 10px -10px 10px 0px;
            padding: 0;
            box-shadow: 10px 10px 10px dimgrey;
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            color: white;
            font-weight: bold;
            display: flex;
            justify-content: center;
            align-items: flex-end;
            cursor: pointer;
        }

        .flexbox > div:hover {
            width: 200px;
        }

    .top-row > div:nth-child(1) { background-image: url("{{ asset('imgs/bg4.jpg') }}"); }
    .top-row > div:nth-child(2) { background-image: url("{{ asset('imgs/bg2.jpg') }}"); }
    .top-row > div:nth-child(3) { background-image: url("{{ asset('imgs/bg11.jpg') }}"); }
    .top-row > div:nth-child(4) { background-image: url("{{ asset('imgs/bg8.jpg') }}"); }
    .top-row > div:nth-child(5) { background-image: url("{{ asset('imgs/bg7.jpg') }}"); }

    .bottom-row > div:nth-child(1) { background-image: url("{{ asset('imgs/bg7.jpg') }}"); }
    .bottom-row > div:nth-child(2) { background-image: url("{{ asset('imgs/bg4.jpg') }}"); }
    .bottom-row > div:nth-child(3) { background-image: url("{{ asset('imgs/bg2.jpg') }}"); }
    .bottom-row > div:nth-child(4) { background-image: url("{{ asset('imgs/bg11.jpg') }}"); }
    .bottom-row > div:nth-child(5) { background-image: url("{{ asset('imgs/bg10.jpg') }}"); }


        /* مربع البحث */
        .search {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }

        .search input {
            color: white;
            font-size: 16px;
            background: transparent;
            width: 250px;
            height: 25px;
            padding: 10px;
            border: solid 3px black;
            outline: none;
            border-radius: 35px;
            transition: all 0.5s;
            box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2),
                0 6px 20px 0 #1a1a1a;
                color: black;
        }

        .search input::placeholder {
            opacity: 0.8;
        }

        .search input:hover {
            box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2),
                0 6px 20px 0 rgba(0, 0, 0, 0.74);
        }
    </style>
</head>

<body>

    <div class="search">
        <input type="text" placeholder="search" required>
    </div>

    <!-- الصف العلوي -->
    <div class="titel">Long dresses</div>
    <div class="flexbox top-row">
        <div onclick="location.href='{{ route('products') }}'">Look 1</div>
        <div onclick="location.href='{{ route('products') }}'">Look 2</div>
        <div onclick="location.href='{{ route('products') }}'">Look 3</div>
        <div onclick="location.href='{{ route('products') }}'">Look 4</div>
        <div onclick="location.href='{{ route('products') }}'">Look 5</div>
    </div>

    <!-- الصف السفلي -->
    <div class="titel">Short dresses</div>
    <div class="flexbox bottom-row">
        <div onclick="location.href='{{ route('products') }}'">Style 1</div>
        <div onclick="location.href='{{ route('products') }}'">Style 2</div>
        <div onclick="location.href='{{ route('products') }}'">Style 3</div>
        <div onclick="location.href='{{ route('products') }}'">Style 4</div>
        <div onclick="location.href='{{ route('products') }}'">Style 5</div>
    </div>

</body>

</html>

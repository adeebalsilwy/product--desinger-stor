<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sections_Body</title>


    <!-- link css -->
    <link rel="stylesheet" href="{{ asset('css/style-sections.css') }}">

    <!-- link js -->
    <script src="{{ asset('js/script.js') }}" defer></script>

</head>
<body>


    <!-- nav js -->
    <script>
        let nav = document.querySelector("nav")
        window.addEventListener("scroll", ()=>{
            if(document.documentElement.scrollTop > 3){
                nav.classList.add("sticky");
            }else{
                nav.classList.remove("sticky");
            }
        });
    </script>

    <div class="container">
        <h3 class="title"> body section </h3>
        <div class="products-container">

            <div class="product" data-name="p-1">
                <img src="{{ asset('imgs/bg8.jpg') }}" alt="">
                <h3>Body Scrub</h3>
                <div class="price">$3.9</div>
            </div>
            <div class="product" data-name="p-2">
                <img src="{{ asset('imgs/bg7.jpg') }}" alt="">
                <h3>Skin whitenings</h3>
                <div class="price">$3.10</div>
            </div>
            <div class="product" data-name="p-3">
                <img src="{{ asset('imgs/bg4.jpg') }}" alt="">
                <h3>Tighten the body</h3>
                <div class="price">$4.7</div>
            </div>
            <div class="product" data-name="p-4">
                <img src="{{ asset('imgs/bg10.jpg') }}" alt="">
                <h3>EMoistueize the body</h3>
                <div class="price">$7.8</div>
            </div>
            <div class="product" data-name="p-5">
                <img src="{{ asset('imgs/bg11.jpg') }}" alt="">
                <h3>Cream tight and moisturizing body</h3>
                <div class="price">$4.1</div>
            </div>
            <div class="product" data-name="p-6">
                <img src="{{ asset('imgs/bg2.jpg') }}" alt="">
                <h3>body whitening</h3>
                <div class="price">$9.8</div>
            </div>
        </div>
    </div>

    <div class="products-preview">

        <div class="preview" data-target="p-1">
            <i class="fas fa-times"></i>
            <img src="{{ asset('imgs/bg8.jpg') }}" alt="">
            <h3> Body Scrub </h3>
            <div class="stars">
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star-haif-alt"></i>
                <span>( 250 )</span>
            </div>
            <p>Bamboo body scrub is an excellent product from clarins.</p>
            <div class="price">$3.9</div>
            <div class="buttons">
                <a href="#" class="buy">buy now</a>
                <a href="#" class="cart">add to cart</a>
                <a href="{{ route('dashboard') }}" class="cart">Edit</a>
            </div>
        </div>

        <div class="preview" data-target="p-2">
            <i class="fas fa-times"></i>
            <img src="{{ asset('imgs/bg7.jpg') }}" alt="">
            <h3> Skin whitenings </h3>
            <div class="stars">
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star-haif-alt"></i>
                <span>( 250 )</span>
            </div>
            <p>An excellent product from clarins is used to lighten the skin .</p>
            <div class="price">$3.10</div>
            <div class="buttons">
                <a href="#" class="buy">buy now</a>
                <a href="#" class="cart">add to cart</a>
                <a href="{{ route('dashboard') }}" class="cart">Edit</a>


            </div>
        </div>

        <div class="preview" data-target="p-3">
            <i class="fas fa-times"></i>
            <img src="{{ asset('imgs/bg4.jpg') }}" alt="">
            <h3> Tighten the body </h3>
            <div class="stars">
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star-haif-alt"></i>
                <span>( 250 )</span>
            </div>
            <p>Used to tighten the body an excellent product from clarins .</p>
            <div class="price">$4.7</div>
            <div class="buttons">
                <a href="#" class="buy">buy now</a>
                <a href="#" class="cart">add to cart</a>
                <a href="{{ route('dashboard') }}" class="cart">Edit</a>

            </div>
        </div>

        <div class="preview" data-target="p-4">
            <i class="fas fa-times"></i>
            <img src="{{ asset('imgs/bg10.jpg') }}" alt="">
            <h3> EMoistueize the body </h3>
            <div class="stars">
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star-haif-alt"></i>
                <span>( 250 )</span>
            </div>
            <p>Used to Moistueize an excellent product from clarins .</p>
            <div class="price">$7.8</div>
            <div class="buttons">
                <a href="#" class="buy">buy now</a>
                <a href="#" class="cart">add to cart</a>
                <a href="{{ route('dashboard') }}" class="cart">Edit</a>

            </div>
        </div>

        <div class="preview" data-target="p-5">
            <i class="fas fa-times"></i>
            <img src="{{ asset('imgs/bg11.jpg') }}" alt="">
            <h3> Cream tight and moisturizing body </h3>
            <div class="stars">
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star-haif-alt"></i>
                <span>( 250 )</span>
            </div>
            <p>sCream used to tighten and moistueize the body an excellent product and is form clarins .</p>
            <div class="price">$4.1</div>
            <div class="buttons">
                <a href="#" class="buy">buy now</a>
                <a href="#" class="cart">add to cart</a>
                <a href="{{ route('dashboard') }}" class="cart">Edit</a>

            </div>
        </div>

        <div class="preview" data-target="p-6">
            <i class="fas fa-times"></i>
            <img src="{{ asset('imgs/bg2.jpg') }}" alt="">
            <h3> body whitening </h3>
            <div class="stars">
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star-haif-alt"></i>
                <span>( 250 )</span>
            </div>
            <p>An excellent product feon Nivea is used to whiten the body .</p>
            <div class="price">$9.8</div>
            <div class="buttons">
                <a href="#" class="buy">buy now</a>
                <a href="#" class="cart">add to cart</a>
                <a href="{{ route('dashboard') }}" class="cart">Edit</a>

            </div>
        </div>

    </div>
    <script>
        let preveiwContainer = document.querySelector('.products-preview');
let previewBox = preveiwContainer.querySelectorAll('.preview');

document.querySelectorAll('.products-container .product').forEach(product =>{
    product.onclick = () =>{
        preveiwContainer.style.display = 'flex';
        let name = product.getAttribute('data-name');
        previewBox.forEach(preview =>{
            let target = preview.getAttribute('data-target');
            if(name == target){
                preview.classList.add('active');
            }
        });
    };
});

previewBox.forEach(close =>{
    close.querySelector('.fa-times').onclick = () =>{
        close.classList.remove('active');
        preveiwContainer.style.display = 'none';
    };
});
    </script>
</body>
</html>


/*----------------- Header Start --------------------- */

/* show dropdown when clicked */

$('#dropdown').click(function () {

  $('.dropdown-content').toggle('.show');

})


/* Hide dropdown when clicked elsewhere  */


$(document).mouseup(function(e) 
{
    var container = $(".dropdown-content");

    // if the target of the click isn't the container nor a descendant of the container
    if (!container.is(e.target) && container.has(e.target).length === 0) 
    {
        container.hide();
    }
});


/*----------------- Header End --------------------- */



function ShowLoginForm(){
		document.getElementById("signup").style.display = "none";
		document.getElementById("login").style.display = "block";
			
	}

function ShowSignupForm(){
		document.getElementById("login").style.display = "none";
		document.getElementById("signup").style.display = "block";
			
	}
/*  index page js*/

	$(".deals-slider").slick({
        dots: true,
        autoplay: true,
        autoplaySpeed:1500,
        speed: 100,
        infinite:true,
        slidesToShow: 3,
        slidesToScroll: 1,
        responsive: [
    {
      breakpoint: 1024,
      settings: {
        slidesToShow: 2,
        slidesToScroll: 1,
        infinite:true,
        dots: false
      }
    },
    {
      breakpoint: 600,
      settings: {
        slidesToShow: 2,
        slidesToScroll: 1
      }
    },
    {
      breakpoint: 480,
      settings: {
        slidesToShow: 1,
        slidesToScroll: 1
      }
    }
    // You can unslick at a given breakpoint now by adding:
    // settings: "unslick"
    // instead of a settings object
  ]
      });

      
      $(".latests-slider").slick({
        dots: true,
        autoplay:true,
        autoplaySpeed:1500,
        speed: 100,
        infinite: true,
        slidesToShow: 4,
        slidesToScroll: 1,
        responsive: [
    {
      breakpoint: 1024,
      settings: {
        slidesToShow: 2,
        slidesToScroll: 1,
        infinite: true,
        dots: false
      }
    },
    {
      breakpoint: 600,
      settings: {
        slidesToShow: 2,
        slidesToScroll: 1
      }
    },
    {
      breakpoint: 480,
      settings: {
        slidesToShow: 1,
        slidesToScroll: 1
      }
    }
    // You can unslick at a given breakpoint now by adding:
    // settings: "unslick"
    // instead of a settings object
  ]
      });

      $(".picks-slider").slick({
        dots: true,
        autoplay: true,
        autoplaySpeed:1500,
        speed: 100,
        infinite: true,
        slidesToShow: 4,
        slidesToScroll: 1,
        responsive: [
    {
      breakpoint: 1024,
      settings: {
        slidesToShow: 2,
        slidesToScroll: 1,
        infinite: true,
        dots: false
      }
    },
    {
      breakpoint: 600,
      settings: {
        slidesToShow: 2,
        slidesToScroll: 1
      }
    },
    {
      breakpoint: 480,
      settings: {
        slidesToShow: 1,
        slidesToScroll: 1
      }
    }
    // You can unslick at a given breakpoint now by adding:
    // settings: "unslick"
    // instead of a settings object
  ]
      });

/*  / index page js*/


/*  / Product Deatils page js*/

$('#product-main-view').slick({
    infinite: true,
    speed: 300,
    dots: false,
    arrows: false,
    fade: true,
    asNavFor: '#product-view',
  });

  $('#product-view').slick({
    slidesToShow: 3,
    slidesToScroll: 1,
    arrows: true,
    centerMode: true,
    focusOnSelect: true,
    asNavFor: '#product-main-view',
    responsive: [
    {
      breakpoint: 1024,
      settings: {
        slidesToShow: 1,
        slidesToScroll: 1,
        infinite: true,
        dots: false
      }
    },
    {
      breakpoint: 600,
      settings: {
        slidesToShow: 2,
        slidesToScroll: 1
      }
    },
    {
      breakpoint: 480,
      settings: {
        slidesToShow: 1,
        slidesToScroll: 1
      }
    }]
  });

  $(".picks-slider").slick({
        dots: true,
        autoplay:false,
        autoplaySpeed:1500,
        speed: 100,
        infinite: true,
        slidesToShow: 4,
        slidesToScroll: 1,
        responsive: [
    {
      breakpoint: 1024,
      settings: {
        slidesToShow: 2,
        slidesToScroll: 1,
        infinite: true,
        dots: false
      }
    },
    {
      breakpoint: 600,
      settings: {
        slidesToShow: 2,
        slidesToScroll: 1
      }
    },
    {
      breakpoint: 480,
      settings: {
        slidesToShow: 1,
        slidesToScroll: 1
      }
    }
    // You can unslick at a given breakpoint now by adding:
    // settings: "unslick"
    // instead of a settings object
  ]
      });

  // PRODUCT ZOOM
  $('#product-main-view .product-view').zoom();

/*  / Product Deatils page js*/

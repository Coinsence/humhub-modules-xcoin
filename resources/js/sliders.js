$(document).ready(function () {
  

  $(".projectsSlider").slick({
    infinite: false,
    slidesToShow: 1,
    variableWidth: true,
    appendArrows: $(".projectsPortfolio .arrows"),

  });

  $(".marketPlacesSlider").slick({
    infinite: false,
    slidesToShow: 1,
    variableWidth: true,
    appendArrows: $(".marketPlacePortfolio .arrows"),

  });

  $(".slick-prev").append('<i class="fas fa-angle-left"></i>');
  $(".slick-next").append('<i class="fas fa-angle-right"></i>');



});

// window.addEventListener("hashchange", function () {

// }, false);



function locationHashChanged() {
  if (location.hash === '#somecoolfeature') {
    somecoolfeature();
  }
}

window.addEventListener('hashchange', locationHashChanged);


// $( window ).resize(function() {
//   if($(window).width()<=600){

//   }else{

//   }
// });


// add or remove item
// $('.add-remove').slick({
//     slidesToShow: 3,
//     slidesToScroll: 3
//   });
//   $('.js-add-slide').on('click', function() {
//     slideIndex++;
//     $('.add-remove').slick('slickAdd','<div><h3>' + slideIndex + '</h3></div>');
//   });

//   $('.js-remove-slide').on('click', function() {
//     $('.add-remove').slick('slickRemove',slideIndex - 1);
//     if (slideIndex !== 0){
//       slideIndex--;
//     }
//   });


$('.badges span a:empty').parent().remove()

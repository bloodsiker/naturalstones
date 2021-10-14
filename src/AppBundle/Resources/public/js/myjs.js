$(document).ready(function() {

	/*Убирание placeholder*/
	 $('input, textarea').focus(function(){
	   $(this).data('placeholder',$(this).attr('placeholder'))
	   $(this).attr('placeholder','');
	 });
	 $('input, textarea').blur(function(){
	   $(this).attr('placeholder',$(this).data('placeholder'));
	 });

	// Слайдер одного товара
	if($('.product-big-slider').length){
		$('.product-big-slider').slick({
			slidesToShow: 1,
			slidesToScroll: 1,
			fade: true,
			speed: 800,
		  	arrows: true,
	      	dots: true,
		});
	}

	// Слайдер похожих товаров
	if($('.similar-products-slider').length){
		$('.similar-products-slider').slick({
			slidesToShow: 4,
			slidesToScroll: 1,
			speed: 800,
			dots: false,
			arrows: true,
			responsive: [
		    {
		      breakpoint: 1019,
		      settings: {
		        slidesToShow: 3,
		      }
		    },
		    {
		      breakpoint: 480,
		      settings: {
		        slidesToShow: 2,
		      }
		    }
		  ]
		});
	}

	// Слайдер в модалке
	if($('.modal-slider').length){
		$('.modal-slider').slick({
			slidesToShow: 2,
			slidesToScroll: 1,
			speed: 800,
			dots: false,
			arrows: true,
		});
	}

	// Слайдер подарков
	if($('.gifts-slider').length){
		$('.gifts-slider').slick({
			slidesToShow: 8,
			slidesToScroll: 1,
			speed: 800,
			dots: false,
			arrows: true,
			responsive: [
		    {
		      breakpoint: 1319,
		      settings: {
		        slidesToShow: 6,
		      }
		    },
		    {
		      breakpoint: 1019,
		      settings: {
		        slidesToShow: 5,
		      }
		    },
		    {
		      breakpoint: 680,
		      settings: {
		        slidesToShow: 4,
		      }
		    },
		    {
		      breakpoint: 530,
		      settings: {
		        slidesToShow: 3,
		      }
		    },
		    {
		      breakpoint: 430,
		      settings: {
		        slidesToShow: 2,
		      }
		    }
		  ]
		});
	}

	// Кастомизация селекта
	if($('.custom-select').length){
		$('.custom-select').select2({
			minimumResultsForSearch: Infinity,
		});
	}

	// Кастомизация селекта сортировки
	if($('.sort-select').length){
		$('.sort-select').select2({
			minimumResultsForSearch: Infinity,
			dropdownCssClass : "custom-sort-dropdown",
		});
	}

	// Работа фильтра
	$(".filter-toggle").click(function () {
		$(".filter-toggle").not($(this)).removeClass("open-filter");
		$(".filter-block").not($(this).parents(".filter-block")).removeClass("zindex");
		$(this).toggleClass("open-filter");
		$(this).parents(".filter-block").toggleClass("zindex");
		if ( $(window).width() > 767 ){
			$(".filter-podmenu").not($(this).next(".filter-podmenu")).fadeOut(200);
			$(this).next(".filter-podmenu").fadeToggle(300);
		}
		else{
			$(".filter-podmenu").not($(this).next(".filter-podmenu")).slideUp(300);
			$(this).next(".filter-podmenu").slideToggle(300);
		}
	});

	$(".select2-container").click(function () {
		$(".filter-podmenu").fadeOut(200);
		$(".filter-toggle").removeClass("open-filter");
	});

	/*Полоса диапазона*/
	if($('.js-range-slider').length){
		var $range = $(".js-range-slider"),
	    $inputFrom = $(".js-input-from"),
	    $inputTo = $(".js-input-to"),
	    instance,
	    min = 20,
	    max = 1000,
	    from = 0,
	    to = 0;

		$range.ionRangeSlider({
		    type: "double",
		    min: min,
		    max: max,
		    from: 20,
		    to: 600,
	   	 	grid: false,
		    onStart: updateInputs,
		    onChange: updateInputs
		});
		instance = $range.data("ionRangeSlider");

		function updateInputs (data) {
			from = data.from;
		    to = data.to;

		    $inputFrom.prop("value", from);
		    $inputTo.prop("value", to);
		}

		$inputFrom.on("input", function () {
		    var val = $(this).prop("value");

		    // validate
		    if (val < min) {
		        val = min;
		    } else if (val > to) {
		        val = to;
		    }

		    instance.update({
		        from: val
		    });
		});

		$inputTo.on("input", function () {
		    var val = $(this).prop("value");

		    // validate
		    if (val < from) {
		        val = from;
		    } else if (val > max) {
		        val = max;
		    }

		    instance.update({
		        to: val
		    });
		});
	}



    /*работа меню*/
	$(".header-buter").on('click', function () {
		$(".header-menu-outer").addClass("visible");
		$(".header-overlay").fadeIn(300);
	});

	$(".header-menu-close, .header-overlay").on('click', function () {
		 $(".header-menu-outer").removeClass("visible");
		 $(".header-overlay").fadeOut(300);
	});

	// Появление фильтра в мобилке
	$('.filter-open').on('click', function(){
		$(".filter-content").fadeIn(300);
	});

	$('.filter-overlay, .catalog-filter-close').on('click', function(){
		$(".filter-content").fadeOut(300);
	});

	// Открытие меню в видео
	$('.video-menu-toggle').on('click', function(){
		$(".video-menu-outer").fadeToggle(300);
		$(".video-options-menu").fadeOut(300);
		$(".video-options-toggle").removeClass("open-filter");
	});

	$('.video-menu-close').on('click', function(){
		$(".video-menu-outer").fadeToggle(300);
	});

	// Появление языков выбора и валют
	$(".video-options-toggle").on('click', function () {
		$(".video-options-toggle").not($(this)).removeClass("open-filter");
		$(this).toggleClass("open-filter");
		$(".video-options-menu").not($(this).next(".video-options-menu")).fadeOut(300);
		$(this).next(".video-options-menu").fadeToggle(300);
		$(".video-menu-outer").fadeOut(300);
	});

	// Работа смены границ в инпуте
	$(".messengers-radio.telegram").on('click', function () {
		$(".messengers-input input").removeClass().addClass('telegram');
	});
	$(".messengers-radio.whatsapp").on('click', function () {
		$(".messengers-input input").removeClass().addClass('whatsapp');
	});
	$(".messengers-radio.viber").on('click', function () {
		$(".messengers-input input").removeClass().addClass('viber');
	});

	/*Табы характеристик*/
	$(".product-description-list a").on('click', function(e){
		e.preventDefault();
		var $searchId = $( $(this).attr("href") );
		$(".product-description-list a").not($(this)).removeClass("active-tab");
		$(this).addClass("active-tab");
		$(".product-description-content").not($searchId).css("display", "none");
		$searchId.fadeIn(0);
	});

	/*Табы характеристик*/
	$(".product-description-trigger a").on('click', function(e){
		e.preventDefault();
		$(this).toggleClass("active-trigger");
		$(this).parent().next('.product-description-tab').slideToggle(300);
	});

	/*Показ быстрого заказа*/
	$(".product-quick-toggle").on('click', function(){
		$(this).toggleClass("opened");
		$('.product-quick-block').slideToggle(300);
	});

	/*модалка*/
	if($('.popup-open').length){
		$('.popup-open').magnificPopup({
		  removalDelay: 300,
		  fixedContentPos: true,
		  callbacks: {
		    beforeOpen: function() {
		       this.st.mainClass = this.st.el.attr('data-effect');
		    }
		  },
		  midClick: true
		});
	}

	if($('.modal-slider').length){
		$(".popup-open").click(function() {
	  		$(".modal-slider").slick('setPosition');
		});
	}

	// Добавление доп. товара
	$(".add-item").click(function(e){
    	e.preventDefault();
    	$(this).toggleClass("added");
    	var text = $(this).find('span').text();
    	$(this).find('span').text(text == "Добавить" ? "Добавлен" : "Добавить");
	});

	// Закрытие модалки
	$(".close-popup").click(function(e){
    	e.preventDefault();
    	$(".mfp-close").click();
	});

	// появление поля ввода открытки
	$(".add-postcard input").change(function(){
        if ($(this).prop('checked')) {
          $('.order-postcard').slideDown(300);
        }
        else {
          $('.order-postcard').slideUp(300);
        }
    });

    // появление адрес получателя
	$(".address-radio-block input").change(function(){
        if ($(".add-address").prop('checked')) {
          $('.add-address-toggle').slideDown(300);
        }
        else {
          $('.add-address-toggle').slideUp(300);
        }
    });

    // Изменения текста оплаты
	$(".payment-radio").click(function(){
    	var text = $(this).data('payment');
    	$("#payment-name").text(text);
	});

	/*работа шапки при скролле*/
	if($('.index-header').length){
		$(window).scroll(function(){
			var headerTop = $( window ).height();
			if ($(this).scrollTop() > headerTop + 60){
				$(".index-header").fadeIn(300);
			}
			else{
				$(".index-header").fadeOut(300);
			}
		});
	}

});

// First we get the viewport height and we multiple it by 1% to get a value for a vh unit
let vh = window.innerHeight * 0.01;
// Then we set the value in the --vh custom property to the root of the document
document.documentElement.style.setProperty('--vh', `${vh}px`);

// We listen to the resize event
window.addEventListener('resize', () => {
  // We execute the same script as before
  let vh = window.innerHeight * 0.01;
  document.documentElement.style.setProperty('--vh', `${vh}px`);
});

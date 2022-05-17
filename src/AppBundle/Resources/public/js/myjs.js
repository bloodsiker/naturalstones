$(document).ready(function() {

	// Адаптация меню под мобильные
	$('.header-menu-desctop').find('.topmenu').each(function(i) {
		$(this).find('.submenu').width(this.offsetWidth);
	})

	$('.phone-input').inputmask("+38 (999) 999-99-99");

	$('.btn-search').on('click', function (e) {
		e.preventDefault();
		$('.container-search').show();
		$('.page').addClass('bluer');
	});

	$('.btn-close').on('click', function (e) {
		e.preventDefault();
		$('.page').removeClass('bluer');
		$('.container-search').hide();
	})

	// При ховере на фото продукта, показываем другую фотку
	$('.image').hover(function () {
		let img = $(this).find('img');
		let hoverImg = img.attr('data-hover-img');
		if (hoverImg) {
			img.attr('src', hoverImg);
		}
	}, function(){
		let img = $(this).find('img');
		let originImg = img.attr('data-src');
		img.attr('src', originImg);
	});

	// показываем просмотренные товары
	let ids = JSON.parse(window.localStorage.getItem('product_ids'));
	if (ids) {
		let url = $('#load-viewed-product').data('url');
		$.ajax({
			type: 'POST',
			url: url,
			data: { ids: ids },
			success: function (response) {
				$('#load-viewed-product').html(response);
			}
		});
	} else {
		$('#section-viewed').hide();
	}

	/*Убирание placeholder*/
	 $('input, textarea').focus(function(){
	   $(this).data('placeholder',$(this).attr('placeholder'))
	   $(this).attr('placeholder','');
	 });
	 $('input, textarea').blur(function(){
	   $(this).attr('placeholder',$(this).data('placeholder'));
	 });

	// Слайдер одного товара
	if ($('.product-big-slider').length) {
		let slickSlider = $('.product-big-slider').slick({
			slidesToShow: 1,
			slidesToScroll: 1,
			fade: true,
			speed: 800,
			arrows: true,
			dots: true,
		});

		// Переключаем слайд при клике на цвет товаров
		$('.switch-colour').on('click', function () {
			let colourId = $(this).data('option-colour');
			let price = $(this).data('price');

			let slide = $('.product-big-slider').find('.product-big-item[data-option-colour=' + colourId + ']');
			if (slide[0]) {
				let indexSlick = slide.data('slick-index');
				slickSlider.slick('slickGoTo', parseInt(indexSlick));
			}

			if (price > 0) {
				$('.price').html(`<span>${price} грн</span>`);
			}
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
		min = $inputFrom.data('min'),
		max = $inputTo.data('max'),
	    from = 0,
	    to = 0;

		$range.ionRangeSlider({
		    type: "double",
		    min: min,
		    max: max,
		    from: $inputFrom.val(),
		    to: $inputTo.val(),
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

	/*Плюс-минус*/
	$('.main-content').on('click', '.qtyplus', function (e) {
		e.preventDefault();
		let fieldName = $(this).siblings('.qty');
		let value = 1;
		let currentVal = parseInt(fieldName.val());
		let maxCount = $(".qtyplus").data("max")
		let recalculate = $(".qtyplus").data("recalculate")
		if (!isNaN(currentVal)) {
			if (currentVal < maxCount) {
				value = currentVal + 1;
				$('.qtyminus').val("-").removeAttr('style');
			} else {
				$(this).val("+").css('color', '#aaa');
				$(this).val("+").css('cursor', 'not-allowed');
			}
		}

		fieldName.val(value);

		if (recalculate) {
			let item_id = fieldName.data('id'),
				url = fieldName.data('url'),
				template = fieldName.data('template'),
				action = fieldName.data('action');

			let filter = { action: action, item_id: item_id, template: template, quantity: value };
			getAjax(url, filter, '#cartContainer', true);
		}
	});

	$('.main-content').on('click', '.qtyminus', function (e) {
		e.preventDefault();
		let fieldName = $(this).siblings('.qty');
		let value = 1;
		let currentVal = parseInt(fieldName.val());
		let recalculate = $(".qtyminus").data("recalculate")
		if (!isNaN(currentVal) && currentVal > 1) {
			value = currentVal - 1;
			$('.qtyplus').val("+").removeAttr('style');
		} else {
			$(this).val("-").css('color', '#aaa');
			$(this).val("-").css('cursor', 'not-allowed');
		}

		fieldName.val(value);

		if (recalculate) {
			let item_id = fieldName.data('id'),
				url = fieldName.data('url'),
				template = fieldName.data('template'),
				action = fieldName.data('action');

			let filter = { action: action, item_id: item_id, template: template, quantity: value };
			getAjax(url, filter, '#cartContainer', true);
		}
	});

	$('.header-cart').on('click','.remove-product', function (e) {
		e.preventDefault();

		let _this = $(this),
			action = _this.data('action'),
			item_id = _this.data('id'),
			url = _this.data('url');

		let filter = { action: action, item_id: item_id };
		getAjax(url, filter, '.header-cart-info', true);
	});

	$('#cartContainer').on('click','.remove-item', function (e) {
		e.preventDefault();

		let _this = $(this),
			action = _this.data('action'),
			template = _this.data('template'),
			item_id = _this.data('id'),
			url = _this.data('url');

		let filter = { action: action, item_id: item_id, template: template };
		getAjax(url, filter, '#cartContainer', true);
	});

	$('#cartContainer').on('input', '.input-recalculate', function () {
		let _this = $(this),
			item_id = _this.data('id'),
			url = _this.data('url'),
			template = _this.data('template'),
			action = _this.data('action'),
			quantity = _this.val();

		let filter = { action: action, item_id: item_id, template: template, quantity: quantity };
		getAjax(url, filter, '#cartContainer', true);
	});

	$('.cart-clean').on('click', function (e) {
		e.preventDefault();

		let _this = $(this),
			url = _this.data('url'),
			template = _this.data('template'),
			action = _this.data('action');

		let filter = { action: action, template: template };
		getAjax(url, filter, '#cartContainer', true);
	})

	let getAjax = function (url, filter, htmlContainer, rewriteCount = false) {
		$.ajax({
			type: 'POST',
			url: url,
			data: filter,
			success: function (response) {
				let container = $(htmlContainer);
				container.html(response);
				if (rewriteCount) {
					rewriteCartCountProduct();
				}
			}
		});
	};

	/* Rewrite count product in cart */
	let rewriteCartCountProduct = function () {
		let container = $('#countProductInCart');
		$('.countProductInCart').text(container.data('count-cart'));
	};

	$('#sendFeedback').on('click', function (e) {
		e.preventDefault();

		let phone = $("input[name='phone']").val();
		let name = $("input[name='name']").val();
		let email = $("input[name='email']").val();
		let message = $("textarea[name='message']").val();
		let url = $('form#formFeedback').attr('action');

		let data = {phone: phone, name: name, email: email, message: message};

		$.ajax({
			type: 'POST',
			url: url,
			data: data,
			success: function (response) {
				if (response.type === 'success') {
					$('.successFeedback').show();
					$('.containerFeedback').hide();
					$('form#formFeedback').trigger('reset');
				}
			}
		});

	});

	$('#nextStep').on('click', function (e) {
		let phone = $("input[name='phone']").val();
		let instagram = $("input[name='instagram']").val();
		let name = $("input[name='name']").val();
		let messenger = $("input[name='messenger']:checked").val();

		$('.phone-error-message, .name-error-message').empty();
		if (messenger === 'telegram' || messenger === 'viber') {
			let lastSymbol = phone.toString().slice(-1);
			if (!phone.length) {
				$('.phone-error-message').text('Укажите номер телефона');
				return false;
			}

			if (lastSymbol === '_') {
				$('.phone-error-message').text('Укажите верно номер телефона');
				return false;
			}
		} else if (messenger === 'instagram') {
			if (!instagram.length) {
				$('.phone-error-message').text('Укажите ссылку на инстаграм');
				return false;
			}
		}

		if (!name.length) {
			$('.name-error-message').text('Укажите имя');
			return false;
		}
	})

	$('.quick-cart').on('click', function (e) {
		e.preventDefault();
		let url = $(this).data('url');
		let phone = $("input[name='phone']").val();
		let instagram = $("input[name='instagram']").val();
		let messenger = $("input[name='messenger']:checked").val();

		$('.error-message').empty();
		if (messenger === 'telegram' || messenger === 'viber') {
			let lastSymbol = phone.toString().slice(-1);
			if (!phone.length) {
				$('.error-message').text('Укажите номер телефона');
				return false;
			}

			if (lastSymbol === '_') {
				$('.error-message').text('Укажите верно номер телефона');
				return false;
			}
		} else if (messenger === 'instagram') {
			if (!instagram.length) {
				$('.error-message').text('Укажите ссылку на инстаграм');
				return false;
			}
		}

		$.ajax({
			type: 'POST',
			url: url,
			data: { phone: phone, messenger: messenger, instagram: instagram },
			success: function (response) {
				if (response.type === 'error') {
					alert(response.message);
					return false;
				}
				if (response.type === 'success') {
					window.location = response.url;
				}
			}
		});
	})

	$('.product-quick-cart').on('click', function (e) {
		e.preventDefault();
		let url = $(this).data('url');
		let phone = $("input[name='phone']").val();
		let instagram = $("input[name='instagram']").val();
		let product = $("input[name='product']").val();
		let messenger = $("input[name='messenger']:checked").val();
		let colourId = null;
		let letter = null;

		if ($('.product-colours-check').length) {
			const isChecked = !!$('input[type="radio"][name=colour_id]:checked').length;
			colourId = $('input[type="radio"][name=colour_id]:checked').val();

			$('#colourError').text('').hide();
			if (!isChecked) {
				$('#colourError').text('Вы не выбрали цвет товара').show();
				return false;
			}
		}

		if ($('.select-letters').length) {
			letter = $('.select-letters option:selected').val();

			$('#letterError').text('').hide();
			if (!letter) {
				$('#letterError').text('Вы не выбрали букву').show();
				return false;
			}
		}

		$('.error-message').empty();
		if (messenger === 'telegram' || messenger === 'viber') {
			let lastSymbol = phone.toString().slice(-1);
			if (!phone.length) {
				$('.error-message').text('Укажите номер телефона');
				return false;
			}

			if (lastSymbol === '_') {
				$('.error-message').text('Укажите верно номер телефона');
				return false;
			}
		} else if (messenger === 'instagram') {
			if (!instagram.length) {
				$('.error-message').text('Укажите ссылку на инстаграм');
				return false;
			}
		}

		$.ajax({
			type: 'POST',
			url: url,
			data: { phone: phone, messenger: messenger, product: product, instagram: instagram, colour_id: colourId, letter: letter },
			success: function (response) {
				if (response.type === 'error') {
					alert(response.message);
					return false;
				}
				if (response.type === 'success') {
					window.location = response.url;
				}
			}
		});
	})

	$('#sendFullCard').on('click', function (e) {
		e.preventDefault();
		let url = $(this).data('url');
		let name = $("input[name='name']").val();
		let email = $("input[name='email']").val();
		let phone = $("input[name='phone']").val();
		let instagram = $("input[name='instagram']").val();
		let address = $("input[name='address']").val();
		let comment = $("input[name='comment']").val();
		let messenger = $("input[name='messenger']").val();

		$.ajax({
			type: 'POST',
			url: url,
			data: { name: name, email: email, phone: phone, address: address,  comment: comment, messenger: messenger, instagram: instagram },
			success: function (response) {
				if (response.type === 'error') {
					alert(response.message);
					return false;
				}
				if (response.type === 'success') {
					window.location = response.url;
				}
			}
		});
	})

	$('.header-cart-link').on('click', function (e) {
		e.preventDefault();
		let _this = $(this),
			url = _this.data('url'),
			action = _this.data('action');

		$.ajax({
			type: 'POST',
			url: url,
			data: { action: action },
			success: function (response) {
				let containerCart = $('.header-cart-info');
				containerCart.html(response);
			}
		});

		$('.header-cart-info').show();
	})

	$(document).click( function(e){
		if ( $(e.target).closest('.header-cart').length ) {
			return;
		}
		$('.header-cart-info').hide();
	});

	$('.product-add-form').on('click','.add-to-cart', function (e) {
		e.preventDefault();

		let _this = $(this),
			item_id = _this.data('id'),
			url = _this.data('url'),
			action = _this.data('action'),
			quantity = $('#quantity').val(),
			colourId = null,
			letter = null;

		if ($('.product-colours-check').length) {
			const isChecked = !!$('input[type="radio"][name=colour_id]:checked').length;
			colourId = $('input[type="radio"][name=colour_id]:checked').val();

			$('#colourError').text('').hide();
			if (!isChecked) {
				$('#colourError').text('Вы не выбрали цвет товара').show();
				return false;
			}
		}

		if ($('.select-letters').length) {
			letter = $('.select-letters option:selected').val();

			$('#letterError').text('').hide();
			if (!letter) {
				$('#letterError').text('Вы не выбрали букву').show();
				return false;
			}
		}

		$.ajax({
			type: 'POST',
			url: url,
			data: { action: action, item_id: item_id, quantity: quantity, colour_id: colourId, letter: letter },
			success: function (response) {
				if (200 === response.code) {
					openPopup(_this);
					$('.header-cart-info').html('');
					$('.countProductInCart').html(response.count);
					$('#modalCountProduct').text(response.count + ' ' + declOfNum(response.count))
					$('#modalTotalProduct').text(response.total)
				}
			}
		});
	});

	let declOfNum = (number) => {
		const titles = ['товар', 'товара', 'товаров'];
		const cases = [2, 0, 1, 1, 1, 2];
		return titles[(number % 100 > 4 && number % 100 < 20) ? 2 : cases[(number % 10 < 5) ? number % 10 : 5]];
	}

	$('#get_more_products').on('click', function (e) {
		e.preventDefault();

		let _this = $(this),
			page = _this.data('page'),
			url = _this.data('url');

		_this.addClass('loading');

		$.ajax({
			type: 'POST',
			url: url,
			data: { page: page },
			success: function (response) {
				$('.catalog-flex').find('.catalog-block:last').after(response);
				_this.removeClass('loading');
				page += 1
				_this.data('page', page);
			}
		});
	})


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
		$(".messengers-input input[name=phone]").show();
		$(".messengers-input input[name=instagram]").val('').hide();
	});
	$(".messengers-radio.whatsapp").on('click', function () {
		$(".messengers-input input").removeClass().addClass('whatsapp');
		$(".messengers-input input[name=phone]").show();
		$(".messengers-input input[name=instagram]").val('').hide();
	});
	$(".messengers-radio.viber").on('click', function () {
		$(".messengers-input input").removeClass().addClass('viber');
		$(".messengers-input input[name=phone]").show();
		$(".messengers-input input[name=instagram]").val('').hide();
	});
	$(".messengers-radio.instagram").on('click', function () {
		$(".messengers-input input").removeClass().addClass('instagram');
		$(".messengers-input input[name=phone]").val('').hide();
		$(".messengers-input input[name=instagram]").show();
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

	$('.quick-cart').on('click', function () {
		let phone = $('.quick-phone').val();
	})

	$('.change-size, .change-zodiac').on('select2:select', function (e) {
		let url = $(e.params.data.element).data('url');
		window.location = url;
	});

	// Модалка
	let openPopup = (el) => {
		if (el !== undefined) {
			$.magnificPopup.open({
				items: {
					src: $(el).attr('href'),
				},
				removalDelay: 300,
				fixedContentPos: true,
				callbacks: {
					beforeOpen: function() {
						this.st.mainClass = $(el).attr('data-effect');
					}
				},
				midClick: true
			});
			$(".modal-slider").slick('setPosition');
		}
	}

	// if ($('.popup-open').length) {
	// 	$('.popup-open').magnificPopup({
	// 		removalDelay: 300,
	// 		fixedContentPos: true,
	// 		callbacks: {
	// 			beforeOpen: function () {
	// 				this.st.mainClass = this.st.el.attr('data-effect');
	// 			}
	// 		},
	// 		midClick: true
	// 	});
	// }
	//
	// if ($('.modal-slider').length) {
	// 	$(".popup-open").click(function () {
	// 		$(".modal-slider").slick('setPosition');
	// 	});
	// }

	// Добавление доп. товара
	// $(".add-item").click(function(e){
    // 	e.preventDefault();
    // 	$(this).toggleClass("added");
    // 	var text = $(this).find('span').text();
    // 	$(this).find('span').text(text == "Добавить" ? "Добавлен" : "Добавить");
	// });

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
	if ($('.index-header').length){
		$(window).scroll(function(){
			let headerTop = $( window ).height();
			if ($(this).scrollTop() > headerTop + 60){
				$('.header-menu-desctop').find('.topmenu').each(function(i) {
					$(this).find('.submenu').width(this.offsetWidth);
				})
				$(".index-header").fadeIn(300);
			} else{
				$(".index-header").fadeOut(300);
			}
		});
	}

	let filterUpdate = function(e) {
		e.preventDefault();

		let url = $('.filter-form input[name=url]').val();
		let filter = '';
		let sortUrl = '';
		let priceUrl = '';
		let stoneUrl = '';
		let colourUrl = '';
		let sort = $('select[name=sort] :selected').val();

		if (sort) {
			sortUrl = 'sort=' + sort;
		}

		priceUrl = 'min_price=' + $('#priceFrom').val() + '&max_price=' + $('#priceTo').val();

		$('.filter-stones input[name=stone]:checked').each(function() {
			if (stoneUrl) {
				stoneUrl += ',' + $(this).val();
			} else {
				stoneUrl = 'stone=' + $(this).val();
			}
		});

		$('.filter-colours input[name=colour]:checked').each(function() {
			if (colourUrl) {
				colourUrl += ',' + $(this).val();
			} else {
				colourUrl = 'colour=' + $(this).val();
			}
		});

		if (priceUrl) {
			if (filter) {
				filter += '&' + priceUrl;
			} else {
				filter += '?' + priceUrl;
			}
		}

		if (stoneUrl) {
			if (filter) {
				filter += '&' + stoneUrl;
			} else {
				filter += '?' + stoneUrl;
			}
		}

		if (colourUrl) {
			if (filter) {
				filter += '&' + colourUrl;
			} else {
				filter += '?' + colourUrl;
			}
		}

		if (sortUrl) {
			if (filter) {
				filter += '&' + sortUrl;
			} else {
				filter += '?' + sortUrl;
			}
		}

		window.location = url + filter;
	}

	$('.btn-filter').on('click', filterUpdate);
	$(document).on('change', '.filter-form .sort-select', filterUpdate);

	$('.slice-this').wTextSlicer({
		height: '350',
		textExpand: 'Развернуть описание',
		textHide: 'Свернуть описание'
	});

});

/*!  wTextSlicer v 1.01 */

jQuery.fn.wTextSlicer = function(options){
	var options = jQuery.extend({
		height: '300',
		textExpand: 'expand text',
		textHide: 'hide text'
	},options);
	return this.each(function() {
		var a = $(this),
			h = a.outerHeight() + 10;
		if ( h > options.height ) {
			a.addClass('slice slice-masked').attr('data-height',h).height(options.height).after('<div class="slice-btn"><span>'+options.textExpand+'</span></div>');
		};
		var bt = $(this).next('.slice-btn').children('span');
		bt.click(function() {
			var ah = parseInt(a.css("height"), 10);
			ah == h ? a.css({'height':options.height}) : a.css({'height':h});
			bt.text(bt.text() == options.textExpand ? options.textHide : options.textExpand);
			a.toggleClass('slice-masked');
		});
	});
};

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

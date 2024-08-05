$(document).ready(function() {
	let selectedStone = 'Ruby'; // По умолчанию
	let selectedStoneImage = null; // По умолчанию
	let selectedStoneId = null; // По умолчанию id пустой
	let stoneSize = 50; // По умолчанию размер камня 50px
	let stoneType = {}; // Объект для хранения типов камней и их изображений с уникальными id
	let stones = []; // Массив для хранения списка камней
	const bracelet = $('#bracelet');
	const lineContainer = $('#line-container');
	const stoneSummary = $('#stone-summary');

	//Объект для хранения типов камней и их изображений с уникальными id
	// const stoneType = {
	//     'yashma': [{'id': 1, 'name': 'Яшма', 'img': 'images/ruby.png'}, {'id': 2, 'name': 'Яшма', 'img': 'images/sapphire.png'}],
	//     'agat': [{'id': 4, 'name': 'Агат', 'img': 'images/emerald.png'}, {'id': 5, 'name': 'Агат', 'img': 'images/sapphire.png'}, {'id': 6, 'name': 'Агат', 'img': 'images/diamond.png'}],
	//     'topaz': [{'id': 7, 'name': 'Топаз', 'img': 'images/ruby.png'}, {'id': 8, 'name': 'Топаз', 'img': 'images/sapphire.png'}],
	//     'rubin': [{'id': 10, 'name': 'Рубин', 'img': 'images/ruby.png'}, {'id': 11, 'name': 'Рубин', 'img': 'images/emerald.png'}, {'id': 12, 'name': 'Рубин', 'img': 'images/diamond.png'}],
	// };

	// Объект для хранения радиусов, количества камней и значений по умолчанию для каждого размера
	const sizeOptions = {
		10: {
			stoneSize: 50,
			radii: {
				16: 135,
				17: 145,
				18: 150,
				19: 155,
				20: 160
			},
			counts: [16, 17, 18, 19, 20],
			defaultCount: 18
		},
		6: {
			stoneSize: 40,
			radii: {
				20: 135,
				21: 140,
				22: 145,
				23: 150,
				24: 155
			},
			counts: [20, 21, 22, 23, 24],
			defaultCount: 22
		}
	};

	function fetchStoneType(size, callback) {
		let url = $('.constructor').data('url');
		$.ajax({
			url: url,
			method: 'POST',
			data: { size: size },
			success: function(response) {
				stones = response.stones;
				stoneType = response.stoneType;
				renderStoneSelect();
				callback();
			},
			error: function(error) {
				console.error('Ошибка при получении данных камней:', error);
			}
		});
	}

	function renderStoneSelect() {
		const stoneSelect = $('#stone-select');
		stoneSelect.empty();
		for (let key in stones) {
			if (stones.hasOwnProperty(key)) {
				let stone = stones[key];
				stoneSelect.append(`<option value="${stone.slug}">${stone.name}</option>`);
			}
		}
	}

	function generateBracelet(num, size) {
		bracelet.empty();
		lineContainer.empty();
		const radius = sizeOptions[size].radii[num] || 150; // Используем радиус по умолчанию, если значение не найдено

		for (let i = 0; i < num; i++) {
			const angle = (i / num) * 2 * Math.PI - Math.PI / 2; // Начинаем с вершины круга
			const x = radius * Math.cos(angle);
			const y = radius * Math.sin(angle);
			const stone = $('<div class="bracelet-stone" data-index="' + i + '"></div>');
			stone.css({
				width: stoneSize + 'px',
				height: stoneSize + 'px',
				top: 'calc(50% + ' + y + 'px)',
				left: 'calc(50% + ' + x + 'px)'
			}).text(i + 1);
			bracelet.append(stone);

			const lineStone = $('<div class="line-stone" data-index="' + i + '"></div>');
			lineStone.css({
				width: '30px',
				height: '30px'
			});
			lineContainer.append(lineStone);
		}

		$('.bracelet-stone').on('click', function() {
			if (selectedStoneId) {
				const index = $(this).data('index');
				const lineStone = $('.line-stone[data-index="' + index + '"]');
				lineStone.css('background-image', selectedStoneImage);
				lineStone.css('background-size', 'cover');
				$(this).css('background-image', selectedStoneImage);
				$(this).css('background-size', 'cover');
				$(this).css('border', 'none');
				$(this).data('id', selectedStoneId);
				$(this).text('');
				updateStoneSummary();
			}
		});
	}

	function generateStoneOptions(type) {
		const stones = stoneType[type];
		const stonePicker = $('.stone-picker');
		stonePicker.empty();
		stones.forEach(stone => {
			const stoneOption = $('<div class="stone-option"></div>');
			stoneOption.css('background-image', `url(${stone.img})`);
			stoneOption.data('id', stone.id);
			stonePicker.append(stoneOption);
		});

		$('.stone-option').on('click', function() {
			selectedStoneId = $(this).data('id');
			selectedStoneImage = $(this).css('background-image');
			$('.stone-option').removeClass('selected');
			$(this).addClass('selected');
		});
	}

	function updateStoneSummary() {
		const stoneCounts = {};
		$('.bracelet-stone').each(function() {
			const stoneImage = $(this).css('background-image');
			const stoneId = $(this).data('id');
			if (stoneImage !== 'none' && stoneId) {
				const stone = Object.values(stoneType).flat().find(s => s.id == stoneId);
				if (stone) {
					if (!stoneCounts[stone.id]) {
						stoneCounts[stone.id] = { count: 0, img: stone.img, name: stone.name };
					}
					stoneCounts[stone.id].count++;
				}
			}
		});

		stoneSummary.empty();
		// stoneSummary.append('<h3 class="title_stone_summary">Заполненные камни:</h3>');
		for (const [stoneId, stoneInfo] of Object.entries(stoneCounts)) {
			const summaryItem = $('<div class="summary-item"></div>');
			summaryItem.append(`<img src=${stoneInfo.img} alt="${stoneId}">`);
			summaryItem.append(`<span>${stoneInfo.name}: ${stoneInfo.count}</span>`);
			stoneSummary.append(summaryItem);
		}
	}

	function updateStoneCountOptions(size) {
		const options = sizeOptions[size].counts;
		const defaultCount = sizeOptions[size].defaultCount;
		const stoneCountSelect = $('#stone-count-select');
		stoneCountSelect.empty();
		options.forEach(option => {
			stoneCountSelect.append(`<option value="${option}">${option}</option>`);
		});
		stoneCountSelect.val(defaultCount); // Устанавливаем значение по умолчанию
		numStones = defaultCount;
		generateBracelet(numStones, size);
	}

	$('.size-button').on('click', function() {
		stoneSummary.empty();
		selectedStoneId = null;
		selectedStoneImage = null;
		$('.size-button').removeClass('selected');
		$(this).addClass('selected');
		const size = $(this).data('size');
		stoneSize = sizeOptions[size].stoneSize;
		// updateStoneCountOptions(size);
		fetchStoneType(size, function() {
			updateStoneCountOptions(size);
			generateStoneOptions($('#stone-select').val());
		});
	});

	$('#stone-select').on('change', function() {
		const selectedType = $(this).val();
		generateStoneOptions(selectedType);
	});

	$('#stone-count-select').on('change', function() {
		stoneSummary.empty();
		numStones = parseInt($(this).val());
		generateBracelet(numStones, $('.size-button.selected').data('size'));
	});

	// Изначально генерируем браслет с 18 камнями, камни для начального типа и размером 10мм
	// generateBracelet(sizeOptions[10].defaultCount, 10);
	// generateStoneOptions($('#stone-select').val());
	// updateStoneCountOptions(10); // Устанавливаем начальные опции для 10мм
	// $('.size-button[data-size="10"]').addClass('selected'); // Устанавливаем начальную выбранную кнопку

	// Изначально получаем данные камней и генерируем браслет с 18 камнями и размером 10мм
	fetchStoneType(10, function() {
		$('.size-button[data-size="10"]').addClass('selected'); // Устанавливаем начальную выбранную кнопку
		generateBracelet(sizeOptions[10].defaultCount, 10);
		generateStoneOptions($('#stone-select').val());
		updateStoneCountOptions(10); // Устанавливаем начальные опции для 10мм
	});



	$('#btn-screenshot').on('click', function() {
		html2canvas(document.querySelector("#constructor-container")).then(canvas => {
			let link = document.createElement('a');
			link.href = canvas.toDataURL();
			link.download = 'screenshot.png';
			link.click();
		});
	});
});
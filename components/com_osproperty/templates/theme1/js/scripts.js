let mainCurrentIndex = 0;
const slideshowMainTrack = document.getElementById('main-slideshowTrack');
const popup = document.getElementById('popup');
const popupImg = document.getElementById('popupImg');
const mainThumbnailContainer = document.getElementById('main-thumbnailContainer');
const thumbnailsPerPage = 5;
const thumbnails = document.getElementById('thumbnails');

function renderMainSlides() {
	slideshowMainTrack.innerHTML = images.map((img, index) => 
		`<div class="main-slide" data-index="${index}" onclick="openPopup('${index}')">
			<img src="${baseUrl}images/osproperty/properties/${propertyId}/medium/${img.src}">
		</div>`
	).join('');
	renderThumbnails();
}

function renderThumbnails() {
	thumbnails.innerHTML = images.map((img, index) => 
		`<img src="${baseUrl}images/osproperty/properties/${propertyId}/thumb/${img.src}" class="thumbnail" data-index="${index}" onclick="showMainImage(${index})">`
	).join('');
}

function showMainImage(index) {
	mainCurrentIndex = index;
	 const isRtl = document.documentElement.getAttribute('dir') === 'rtl';
	const offset = isRtl ? index * 100 : -index * 100;
	slideshowMainTrack.style.transform = `translateX(${offset}%)`;
	updateThumbnails();
	showThumbnails(mainCurrentIndex);
}

function updateThumbnails() {
	document.querySelectorAll('.main-thumbnails img').forEach(img => img.classList.remove('active'));
	document.querySelector(`.main-thumbnails img[data-index="${mainCurrentIndex}"]`).classList.add('active');
}

function showThumbnails(index) {
	const totalThumbnails = images.length;
	let startIndex = Math.floor(index / thumbnailsPerPage) * thumbnailsPerPage;

	// Ensure the startIndex is within bounds
	if (startIndex + thumbnailsPerPage > totalThumbnails) {
		startIndex = totalThumbnails - thumbnailsPerPage;
	}

	if (startIndex < 0) {
		startIndex = 0;
	}

	// Move the thumbnail container to show the selected group
	const maxOffset = (totalThumbnails - thumbnailsPerPage) * 90; // 90px is the width of the thumbnail + margin
	const newOffset = Math.min(startIndex * 90, maxOffset);
	thumbnails.style.transform = `translateX(-${newOffset}px)`;
}

function openPopup(src) 
{   
	document.getElementById('slideshowContainer').style.display = 'flex';
	renderSlides();
	showImage(src);
	startSlideshow();
	setupThumbnails();
}

function closePopup() {
	//popup.style.display = 'none';
}

document.getElementById('main-prevBtn').addEventListener('click', () => {
	mainCurrentIndex = (mainCurrentIndex - 1 + images.length) % images.length;
	showMainImage(mainCurrentIndex);
});

document.getElementById('main-nextBtn').addEventListener('click', () => {
	mainCurrentIndex = (mainCurrentIndex + 1) % images.length;
	showMainImage(mainCurrentIndex);
});

popup.addEventListener('click', closePopup);

renderMainSlides();
showMainImage(0);
//end main slideshow//

let currentIndex = 0;
let slideshowInterval;
let isUserInteracting = false;
let isPlaying = false;

const intervalTime = 3000;
const transitionTime = 500;

const slideshowTrack = document.getElementById('slideshowTrack');
const pauseBtn = document.getElementById('pauseBtn');

function renderSlides() {
	slideshowTrack.innerHTML = images.map(img => 
		`<img src="${baseUrl}images/osproperty/properties/${propertyId}/medium/${img.src}" class="slideshow-image">`
	).join('');
	if (images.length <= 1) {
        document.getElementById('prevBtn').style.display = 'none';
        document.getElementById('nextBtn').style.display = 'none';
    }
}

function showImage(index) {
	currentIndex = index;
	const isRtl = document.documentElement.getAttribute('dir') === 'rtl';
	const offset = isRtl ? index * 100 : -index * 100;
	slideshowTrack.style.transform = `translateX(${offset}%)`;
	document.getElementById('photoDescription').textContent = images[index].description;
}

function startSlideshow() {
	if (!isPlaying) {
		isPlaying = true;
		pauseBtn.textContent = "❙❙"; // Pause icon
		clearInterval(slideshowInterval);
		slideshowInterval = setInterval(() => {
			goToNextSlide();
		}, intervalTime);
	}
}

function pauseSlideshow() {
	if (isPlaying) {
		isPlaying = false;
		pauseBtn.textContent = "▶"; // Play icon
		clearInterval(slideshowInterval);
	} else {
		startSlideshow();
	}
}

function goToNextSlide() {
	if (slideshowTrack.style.transition) return;
	if(images.length > 1)
	{
		currentIndex = (currentIndex + 1) % images.length;
		updateSlide();
	}
}

function goToPrevSlide() {
	if (slideshowTrack.style.transition) return;
	if(images.length > 1)
	{
		currentIndex = (currentIndex - 1 + images.length) % images.length;
		updateSlide();
	}
}

function updateSlide() {
	slideshowTrack.style.transition = `transform ${transitionTime}ms ease-in-out`;
	const isRtl = document.documentElement.getAttribute('dir') === 'rtl';
	const offset = isRtl ? currentIndex * 100 : -currentIndex * 100;
	slideshowTrack.style.transform = `translateX(${offset}%)`;
	document.getElementById('photoDescription').textContent = images[currentIndex].description;
	setTimeout(() => {
		slideshowTrack.style.transition = '';
	}, transitionTime);
}

function setupThumbnails() {
	document.getElementById('thumbnailContainer').innerHTML = images.map((img, index) => 
		`<img src="${baseUrl}images/osproperty/properties/${propertyId}/thumb/${img.src}" onclick="showImage(${index})">`
	).join('');
}

document.getElementById('closeBtn').addEventListener('click', () => {
	document.getElementById('slideshowContainer').style.display = 'none';
	clearInterval(slideshowInterval);
});

document.getElementById('gridBtn').addEventListener('click', () => {
	document.getElementById('slideshowContainer').classList.toggle('grid-active');
});

document.getElementById('prevBtn').addEventListener('click', () => {
	//handleUserInteraction();
	//currentIndex = (currentIndex - 1 + images.length) % images.length;
	//showImage(currentIndex);
	stopSlideshow();
	goToPrevSlide();

	startSlideshow();
});

document.getElementById('nextBtn').addEventListener('click', () => {
	stopSlideshow();
	goToNextSlide();
	startSlideshow();
});

function handleUserInteraction() {
	isUserInteracting = true;
	stopSlideshow();
	setTimeout(() => {
		isUserInteracting = false;
		startSlideshow(); // Sau 3 giây không có tương tác, bắt đầu lại slideshow tự động
	}, 3000);
}

function stopSlideshow() {
	clearInterval(slideshowInterval);
}

pauseBtn.addEventListener('click', pauseSlideshow);
// Listings
jQuery(".listing-grid").addClass("listing-active");

jQuery(".listing-full").click(function() {
	jQuery(".listing").addClass("listing-full");
	jQuery(this).addClass("listing-active");
	jQuery('.listing-grid').removeClass("listing-active");
});

jQuery(".listing-grid").click(function() {
	jQuery(".listing").removeClass("listing-full");
	jQuery(this).addClass("listing-active");
	jQuery('.listing-full').removeClass("listing-active");
});


function openSlideshow(index) {
	//currentIndex = index;
	//popupImage.src = images[currentIndex];
	//slideshowPopup.style.display = 'flex';
	document.getElementById('slideshowContainer').style.display = 'flex';
	renderSlides();
	showImage(index);
	startSlideshow();
	setupThumbnails();
}

let currentIndex = 0;
let slideshowInterval;
let isUserInteracting = false;
let isPlaying = false;

const intervalTime = 3000;
const transitionTime = 500;

const slideshowTrack = document.getElementById('slideshowTrack');
const pauseBtn = document.getElementById('pauseBtn');

function renderSlides() {
	slideshowTrack.innerHTML = images1.map(img => 
		`<img src="${baseUrl}images/osproperty/properties/${propertyId}/medium/${img.src}" class="slideshow-image">`
	).join('');
	if (images1.length <= 1) {
        document.getElementById('prevBtn').style.display = 'none';
        document.getElementById('nextBtn').style.display = 'none';
    }
}

function showImage(index) {
	currentIndex = index;
	const isRtl = document.documentElement.getAttribute('dir') === 'rtl';
	const offset = isRtl ? index * 100 : -index * 100;
	slideshowTrack.style.transform = `translateX(${offset}%)`;
	document.getElementById('photoDescription').textContent = images1[index].description;
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

	if(images1.length > 1)
	{
		currentIndex = (currentIndex + 1) % images1.length;
		updateSlide();
	}
}

function goToPrevSlide() {
	if (slideshowTrack.style.transition) return;

	if(images1.length > 1)
	{
		currentIndex = (currentIndex - 1 + images1.length) % images1.length;
		updateSlide();
	}
}

function updateSlide() {
	slideshowTrack.style.transition = `transform ${transitionTime}ms ease-in-out`;
	const isRtl = document.documentElement.getAttribute('dir') === 'rtl';
	const offset = isRtl ? currentIndex * 100 : -currentIndex * 100;
	slideshowTrack.style.transform = `translateX(${offset}%)`;

	setTimeout(() => {
		slideshowTrack.style.transition = '';
	}, transitionTime);
}

function setupThumbnails() {
	document.getElementById('thumbnailContainer').innerHTML = images1.map((img, index) => 
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
		startSlideshow();
	}, 3000);
}

function stopSlideshow() {
	clearInterval(slideshowInterval);
}

pauseBtn.addEventListener('click', pauseSlideshow);
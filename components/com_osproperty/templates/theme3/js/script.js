const largeImageContainer = document.getElementById('largeImageContainer');
const smallImagesContainer = document.getElementById('smallImagesContainer');

largeImageContainer.innerHTML = `<img src="${images[0]}" class="theme3_property_photos" alt="" data-index="0" onClick="openSlideshow(0);" id="first_property_picture">`;

for (let i = 1; i < images.length; i++) {
	const item = document.createElement('div');
	item.classList.add('gallery-item-small');
	item.innerHTML = `<img src="${images[i]}" class="theme3_property_photos" alt="Image ${i + 1}" data-index="${i}">`;
	item.querySelector('img').addEventListener('click', (e) => openSlideshow(parseInt(e.target.getAttribute('data-index'))));
	if (i === images.length - 1) {
		const moreImage = document.createElement('div');
		moreImage.classList.add('more-image');
		moreImage.textContent = moreImageText;
		if(count_photos >  count_photos1)
		{
			item.appendChild(moreImage);
		}
	}

	smallImagesContainer.appendChild(item);
}

// Nếu tổng số ảnh (bao gồm ảnh lớn) là số lẻ, thêm thẻ chứa text ngẫu nhiên
if ((images.length) % 2 !== 1 || images.length <=2) 
{
	const textItem = document.createElement('div');
	textItem.classList.add('gallery-item-small', 'random-text');
	textItem.textContent = randomTexts[Math.floor(Math.random() * randomTexts.length)];
	smallImagesContainer.appendChild(textItem);
}

if(images.length == 1 )
{
	smallImagesContainer.style.gridTemplateColumns = '1fr';
}

function updateGalleryForMobile() {
    const gallery = document.querySelector('.gallery');
    const images = gallery.querySelectorAll('img');

    if (window.innerWidth < 768) {
        images.forEach((img, index) => {
            img.style.display = index === 0 ? 'block' : 'none'; // Chỉ hiển thị ảnh đầu tiên
        });
    } else {
        images.forEach(img => {
            img.style.display = 'block'; // Hiển thị lại toàn bộ ảnh khi về màn hình lớn
        });
    }
}

// Gọi khi tải trang và khi resize
window.addEventListener('load', updateGalleryForMobile);
window.addEventListener('resize', updateGalleryForMobile);

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

const intervalTime = 3000;
const transitionTime = 500;

const slideshowTrack = document.getElementById('slideshowTrack');

function renderSlides() {
	slideshowTrack.innerHTML = images1.map(img => 
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
	document.getElementById('photoDescription').textContent = images1[index].description;
}

function startSlideshow() {
	clearInterval(slideshowInterval);
	slideshowInterval = setInterval(() => {
		goToNextSlide();
	}, intervalTime);
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
	//slideshowTrack.style.transform = `translateX(-${currentIndex * 100}%)`;

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
let currentSlide = 0; // Index of the current slide
const slides = document.querySelectorAll('.slider-item'); // Get all slides
const totalSlides = slides.length;
let autoSlideInterval;

function showSlide(index) {
    // Скрываем все слайды
    slides.forEach(slide => {
        slide.classList.remove('active');
    });
    
    // Показываем текущий слайд
    slides[index].classList.add('active');
}

function startAutoSlide() {
    // Очищаем предыдущий интервал, если он существует
    if (autoSlideInterval) {
        clearInterval(autoSlideInterval);
    }
    // Запускаем новый интервал
    autoSlideInterval = setInterval(showNextSlide, 5000);
}

// Show the next slide
function showNextSlide() {
    currentSlide = (currentSlide + 1) % totalSlides;
    showSlide(currentSlide);
    startAutoSlide(); // Перезапускаем таймер после ручного переключения
}

// Show the previous slide
function showPreviousSlide() {
    currentSlide = (currentSlide - 1 + totalSlides) % totalSlides;
    showSlide(currentSlide);
    startAutoSlide(); // Перезапускаем таймер после ручного переключения
}

// Инициализация слайдера при загрузке страницы
document.addEventListener('DOMContentLoaded', () => {
    showSlide(currentSlide);
    startAutoSlide(); // Запускаем автоматическое переключение
});
 class Presentation {
            constructor() {
                this.currentSlide = 0;
                this.totalSlides = 4;
                this.slidesContainer = document.getElementById('slidesContainer');
                this.progressIndicator = document.getElementById('progressIndicator');
                this.prevBtn = document.getElementById('prevBtn');
                this.nextBtn = document.getElementById('nextBtn');
                
                this.init();
            }
            
            init() {
                this.prevBtn.addEventListener('click', () => this.prevSlide());
                this.nextBtn.addEventListener('click', () => this.nextSlide());
                
                // Обработчики клавиатуры
                document.addEventListener('keydown', (e) => {
                    if (e.key === 'ArrowRight') this.nextSlide();
                    if (e.key === 'ArrowLeft') this.prevSlide();
                });
                
                this.updateView();
            }
            
            nextSlide() {
                if (this.currentSlide < this.totalSlides - 1) {
                    this.currentSlide++;
                    this.updateView();
                }
            }
            
            prevSlide() {
                if (this.currentSlide > 0) {
                    this.currentSlide--;
                    this.updateView();
                }
            }
            
            updateView() {
                // Перемещаем контейнер слайдов
                const translateX = -this.currentSlide * 100;
                this.slidesContainer.style.transform = `translateX(${translateX}vw)`;
                
                // Обновляем UI
                this.progressIndicator.textContent = `Страница ${this.currentSlide + 1} из ${this.totalSlides}`;
                this.prevBtn.disabled = this.currentSlide === 0;
                this.nextBtn.disabled = this.currentSlide === this.totalSlides - 1;
            }
            
            goToSlide(slideIndex) {
                if (slideIndex >= 0 && slideIndex < this.totalSlides) {
                    this.currentSlide = slideIndex;
                    this.updateView();
                }
            }
        }
        
        // Инициализация при загрузке
        document.addEventListener('DOMContentLoaded', () => {
            new Presentation();
        });
// modules/video/assets/js/video.js
class VideoModule {
    constructor() {
        this.videoGenerator = new VideoGenerator();
        this.init();
    }

    init() {
        console.log('🔄 VideoModule инициализируется...');
        this.setupEventListeners();
        this.updateSystemStatus();
        setInterval(() => this.updateSystemStatus(), 60000);
        console.log('✅ VideoModule инициализирован');
    }

    setupEventListeners() {
        console.log('🔧 Настраиваю обработчики событий...');
        
        // Поиск камер
        const searchInput = document.getElementById('cameraSearch');
        if (searchInput) {
            searchInput.addEventListener('input', (e) => this.filterCameras(e.target.value));
            console.log('✅ Поиск камер настроен');
        }

        // Закрытие модального окна
        const closeBtn = document.querySelector('.close');
        if (closeBtn) {
            closeBtn.addEventListener('click', () => this.closeVideoModal());
            console.log('✅ Кнопка закрытия настроена');
        } else {
            console.log('❌ Кнопка закрытия не найдена');
        }

        // Закрытие по клику вне модального окна
        window.addEventListener('click', (event) => {
            const modal = document.getElementById('videoModal');
            if (event.target === modal) {
                this.closeVideoModal();
            }
        });

        console.log('✅ Все обработчики настроены');
    }

    showCamera(cameraId, cameraName, cameraLocation) {
        console.log('🎥 Пытаюсь открыть камеру:', cameraId);
        
        const modal = document.getElementById('videoModal');
        const videoPlayer = document.getElementById('videoPlayer');
        
        if (!modal) {
            console.log('❌ Модальное окно не найдено');
            return;
        }
        
        if (!videoPlayer) {
            console.log('❌ Видеоплеер не найден');
            return;
        }

        // Обновляем информацию
        document.getElementById('modalTitle').textContent = cameraName;
        document.getElementById('cameraInfo').textContent = cameraLocation;
        document.getElementById('connectionStatus').textContent = '● LIVE';
        document.getElementById('connectionStatus').style.color = '#28a745';
        
        // Очищаем предыдущее видео
        videoPlayer.srcObject = null;
        
        // Генерируем программное видео
        console.log('🔄 Генерирую видео...');
        const stream = this.videoGenerator.generateCameraFeed(
            cameraId, 
            cameraName, 
            cameraLocation
        );
        
        videoPlayer.srcObject = stream;
        videoPlayer.play().then(() => {
            console.log('✅ Видео запущено');
        }).catch(error => {
            console.log('❌ Ошибка воспроизведения:', error);
        });
        
        // Показываем модальное окно
        modal.style.display = 'block';
        console.log('✅ Модальное окно открыто');
    }

    closeVideoModal() {
        console.log('🔒 Закрываю модальное окно...');
        const modal = document.getElementById('videoModal');
        if (modal) {
            modal.style.display = 'none';
        }
        
        const videoPlayer = document.getElementById('videoPlayer');
        if (videoPlayer) {
            videoPlayer.pause();
            videoPlayer.srcObject = null;
        }
        
        if (this.videoGenerator) {
            this.videoGenerator.stopAnimation();
        }
        console.log('✅ Модальное окно закрыто');
    }

    filterCameras(searchTerm) {
        const cards = document.querySelectorAll('.camera-card');
        const term = searchTerm.toLowerCase();
        
        cards.forEach(card => {
            const name = card.querySelector('.camera-name').textContent.toLowerCase();
            const location = card.querySelector('.camera-location').textContent.toLowerCase();
            
            card.style.display = (name.includes(term) || location.includes(term)) ? 'block' : 'none';
        });
    }

    updateSystemStatus() {
        const now = new Date();
        const lastUpdateElement = document.getElementById('lastUpdate');
        const uptimeElement = document.getElementById('uptime');
        
        if (lastUpdateElement) {
            lastUpdateElement.textContent = now.toLocaleTimeString();
        }
        
        if (uptimeElement) {
            // Простой расчет uptime (можно заменить на реальный)
            const startTime = new Date(now.getTime() - 2 * 60 * 60 * 1000); // 2 часа назад
            const diff = now - startTime;
            const days = Math.floor(diff / (1000 * 60 * 60 * 24));
            const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
            
            uptimeElement.textContent = `${days}д ${hours}ч ${minutes}м`;
        }
    }

    // Быстрые действия
    showAllCameras() {
        const cards = document.querySelectorAll('.camera-card');
        cards.forEach(card => card.style.display = 'block');
        console.log('✅ Показаны все камеры');
    }

    showOnlyOnline() {
        const cards = document.querySelectorAll('.camera-card');
        cards.forEach(card => {
            const status = card.querySelector('.camera-status').textContent;
            card.style.display = status.includes('ONLINE') ? 'block' : 'none';
        });
        console.log('✅ Показаны только онлайн камеры');
    }

    refreshAll() {
        location.reload();
        console.log('🔄 Обновляю страницу...');
    }

    emergencyAlert() {
        alert('🚨 ТРЕВОГА! Уведомление отправлено старшему смены!');
        console.log('🚨 Тревога активирована');
    }

    takeScreenshot() {
        const videoPlayer = document.getElementById('videoPlayer');
        if (!videoPlayer) {
            console.log('❌ Видеоплеер не найден для скриншота');
            return;
        }
        
        const canvas = document.createElement('canvas');
        const ctx = canvas.getContext('2d');
        
        canvas.width = videoPlayer.videoWidth;
        canvas.height = videoPlayer.videoHeight;
        ctx.drawImage(videoPlayer, 0, 0, canvas.width, canvas.height);
        
        // Создаем ссылку для скачивания
        const link = document.createElement('a');
        link.download = `screenshot_${this.cameraId}_${new Date().toISOString().replace(/[:.]/g, '-')}.png`;
        link.href = canvas.toDataURL();
        link.click();
        
        console.log('📸 Скриншот сохранен:', link.download);
        alert(`📸 Снимок камеры "${this.cameraId}" сохранен!`);
    }
}

// Инициализация при загрузке страницы
document.addEventListener('DOMContentLoaded', () => {
    console.log('📄 DOM загружен, создаю VideoModule...');
    window.videoModule = new VideoModule();
});
// modules/video/assets/js/video.js
class VideoModule {
    constructor() {
        this.testStreams = {
            'kpp': 'rtsp://demo:demo@ipvmdemo.dyndns.org:5541/onvif-media/media.amp',
            'hall': 'rtsp://wowzaec2demo.streamlock.net/vod/mp4:BigBuckBunny_115k.mp4',
            'parking': 'rtsp://184.72.239.149/vod/mp4:BigBuckBunny_115k.mp4'
        };
        this.videoGenerator = new VideoGenerator();
        this.init();
    }

    async showCamera(cameraId, cameraName, cameraLocation) {
        console.log('🎥 Пытаюсь открыть камеру:', cameraId);
        
        const modal = document.getElementById('videoModal');
        const videoPlayer = document.getElementById('videoPlayer');
        
        if (!modal || !videoPlayer) {
            console.log('❌ Элементы не найдены');
            return;
        }

        // Обновляем информацию
        document.getElementById('modalTitle').textContent = cameraName;
        document.getElementById('cameraInfo').textContent = cameraLocation;
        document.getElementById('connectionStatus').textContent = '● ПОДКЛЮЧАЕМСЯ...';
        document.getElementById('connectionStatus').style.color = '#ffc107';

        // Очищаем предыдущее видео
        videoPlayer.srcObject = null;
        
        try {
            // Пробуем подключиться к тестовому RTSP
            await this.connectToTestStream(cameraId, videoPlayer);
            document.getElementById('connectionStatus').textContent = '● LIVE (RTSP)';
            document.getElementById('connectionStatus').style.color = '#28a745';
        } catch (error) {
            console.log('❌ RTSP недоступен, переключаюсь на генерацию:', error);
            // Если RTSP не доступен - используем генерацию
            this.useVideoGenerator(cameraId, cameraName, cameraLocation, videoPlayer);
        }
        
        // Показываем модальное окно
        modal.style.display = 'block';
    }

    async connectToTestStream(cameraId, videoPlayer) {
        return new Promise((resolve, reject) => {
            // Здесь будет код для подключения к RTSP
            // Пока эмулируем задержку подключения
            setTimeout(() => {
                if (Math.random() > 0.3) { // 70% успешных подключений для демо
                    // В реальности здесь будет HLS прокси для RTSP
                    this.useVideoGenerator(cameraId, '', '', videoPlayer);
                    resolve();
                } else {
                    reject(new Error('RTSP stream not available'));
                }
            }, 1000);
        });
    }

    useVideoGenerator(cameraId, cameraName, cameraLocation, videoPlayer) {
        document.getElementById('connectionStatus').textContent = '● LIVE (ДЕМО)';
        document.getElementById('connectionStatus').style.color = '#17a2b8';
        
        const stream = this.videoGenerator.generateCameraFeed(
            cameraId, 
            cameraName, 
            cameraLocation
        );
        
        videoPlayer.srcObject = stream;
        videoPlayer.play().catch(error => {
            console.log('❌ Ошибка воспроизведения:', error);
        });
    }
}
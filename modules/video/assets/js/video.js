// modules/video/assets/js/video.js
class VideoModule {
    constructor() {
        this.videoGenerator = new VideoGenerator();
        this.init();
    }

    init() {
        this.setupEventListeners();
        console.log('VideoModule инициализирован');
    }

    setupEventListeners() {
        // Поиск камер
        const searchInput = document.getElementById('cameraSearch');
        if (searchInput) {
            searchInput.addEventListener('input', (e) => this.filterCameras(e.target.value));
        }

        // Закрытие модального окна
        const closeBtn = document.querySelector('.close');
        if (closeBtn) {
            closeBtn.addEventListener('click', () => this.closeVideoModal());
        }

        // Закрытие по клику вне модального окна
        window.addEventListener('click', (event) => {
            const modal = document.getElementById('videoModal');
            if (event.target === modal) {
                this.closeVideoModal();
            }
        });

        // Закрытие по ESC
        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                this.closeVideoModal();
            }
        });
    }

    showCamera(cameraId, cameraName, cameraLocation) {
        document.getElementById('modalTitle').textContent = cameraName;
        document.getElementById('cameraInfo').textContent = cameraLocation;
        document.getElementById('connectionStatus').textContent = '● LIVE';
        document.getElementById('connectionStatus').style.color = '#28a745';
        
        const videoPlayer = document.getElementById('videoPlayer');
        
        // Очищаем предыдущее видео
        videoPlayer.srcObject = null;
        
        // Генерируем программное видео
        const stream = this.videoGenerator.generateCameraFeed(
            cameraId, 
            cameraName, 
            cameraLocation
        );
        
        videoPlayer.srcObject = stream;
        videoPlayer.play();
        
        document.getElementById('videoModal').style.display = 'block';
    }

    closeVideoModal() {
        document.getElementById('videoModal').style.display = 'none';
        const videoPlayer = document.getElementById('videoPlayer');
        videoPlayer.pause();
        videoPlayer.srcObject = null;
        this.videoGenerator.stopAnimation();
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
}

// Инициализация при загрузке страницы
document.addEventListener('DOMContentLoaded', () => {
    window.videoModule = new VideoModule();
});
// Методы быстрых действий
showAllCameras() {
    const cards = document.querySelectorAll('.camera-card');
    cards.forEach(card => card.style.display = 'block');
}

showOnlyOnline() {
    const cards = document.querySelectorAll('.camera-card');
    cards.forEach(card => {
        const status = card.querySelector('.camera-status').textContent;
        card.style.display = status.includes('ONLINE') ? 'block' : 'none';
    });
}

refreshAll() {
    location.reload();
}

emergencyAlert() {
    alert('🚨 ТРЕВОГА! Уведомление отправлено старшему смены!');
    // Здесь будет интеграция с системой оповещений
}
updateSystemStatus() {
    const now = new Date();
    document.getElementById('lastUpdate').textContent = now.toLocaleTimeString();
    
    // Простой расчет uptime (можно заменить на реальный)
    const startTime = new Date(now.getTime() - 2 * 60 * 60 * 1000); // 2 часа назад
    const diff = now - startTime;
    const days = Math.floor(diff / (1000 * 60 * 60 * 24));
    const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
    
    document.getElementById('uptime').textContent = `${days}д ${hours}ч ${minutes}м`;
}

// В init() добавляем:
init() {
    this.setupEventListeners();
    this.updateSystemStatus();
    // Обновляем статус каждую минуту
    setInterval(() => this.updateSystemStatus(), 60000);
    console.log('VideoModule инициализирован');
}
setupHotkeys() {
    document.addEventListener('keydown', (e) => {
        // Ctrl + цифра (1-4) для быстрого переключения камер
        if (e.ctrlKey && e.key >= '1' && e.key <= '4') {
            e.preventDefault();
            const cameraIndex = parseInt(e.key) - 1;
            this.openCameraByIndex(cameraIndex);
        }
        
        // Space для паузы/плей
        if (e.code === 'Space' && document.getElementById('videoModal').style.display === 'block') {
            e.preventDefault();
            this.togglePlayPause();
        }
        
        // Escape для закрытия модального окна
        if (e.code === 'Escape') {
            this.closeVideoModal();
        }
    });
}

openCameraByIndex(index) {
    const cameras = [
        {id: 'kpp', name: 'КПП Главный', location: 'Центральный вход'},
        {id: 'hall', name: 'Холл 2 этаж', location: 'Основной холл'},
        {id: 'parking', name: 'Парковка', location: 'Южная парковка'},
        {id: 'warehouse', name: 'Склад №1', location: 'Основной склад'}
    ];
    
    if (cameras[index]) {
        this.showCamera(cameras[index].id, cameras[index].name, cameras[index].location);
    }
}

togglePlayPause() {
    const videoPlayer = document.getElementById('videoPlayer');
    if (videoPlayer.paused) {
        videoPlayer.play();
    } else {
        videoPlayer.pause();
    }
}

// Обновляем init():
init() {
    this.setupEventListeners();
    this.setupHotkeys(); // ← добавляем эту строку
    this.updateSystemStatus();
    setInterval(() => this.updateSystemStatus(), 60000);
    console.log('VideoModule инициализирован');
}
takeScreenshot() {
    const videoPlayer = document.getElementById('videoPlayer');
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
    
    // Уведомление
    alert(`📸 Снимок камеры "${this.cameraId}" сохранен!`);
}

// Обновляем showCamera() чтобы запоминать текущую камеру:
showCamera(cameraId, cameraName, cameraLocation) {
    this.cameraId = cameraId; // ← запоминаем ID камеры
    // ... остальной код
}
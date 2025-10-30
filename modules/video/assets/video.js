// modules/video/assets/js/video.js
class VideoModule {
    constructor() {
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
        
        // Демо-режим
        const videoPlayer = document.getElementById('videoPlayer');
        videoPlayer.innerHTML = `
            <div style="padding: 50px; text-align: center; color: white; background: #000;">
                <h3>Демо-режим: ${cameraName}</h3>
                <p>В реальной системе здесь будет LIVE-видео с камеры</p>
                <p>ID камеры: ${cameraId}</p>
                <p>📍 ${cameraLocation}</p>
                <p>🕒 Режим: REAL-TIME</p>
            </div>
        `;
        
        document.getElementById('videoModal').style.display = 'block';
    }

    closeVideoModal() {
        document.getElementById('videoModal').style.display = 'none';
        const videoPlayer = document.getElementById('videoPlayer');
        videoPlayer.pause();
        videoPlayer.src = '';
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
// Методы для реальных камер (будем использовать когда получим доступ)
connectToCameraRTSP(cameraId) {
    // Будет подключаться к RTSP потоку
    console.log('Подключаемся к RTSP камеры:', cameraId);
    // return fetch(`/api/camera/${cameraId}/connect`);
}

getCameraStreamURL(cameraId) {
    // Будет возвращать URL для видео потока
    return `/api/stream/${cameraId}/live.m3u8`;
}
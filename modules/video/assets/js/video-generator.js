// modules/video/assets/js/video-generator.js
class VideoGenerator {
    constructor() {
        this.canvas = document.createElement('canvas');
        this.ctx = this.canvas.getContext('2d');
        this.canvas.width = 640;
        this.canvas.height = 480;
        this.isPlaying = false;
        this.animationId = null;
    }

    generateCameraFeed(cameraId, cameraName, location) {
        this.cameraId = cameraId;
        this.cameraName = cameraName;
        this.location = location;
        
        this.startAnimation();
        return this.canvas.captureStream(25); // 25 FPS
    }

    startAnimation() {
        if (this.isPlaying) return;
        
        this.isPlaying = true;
        this.animationId = requestAnimationFrame(this.animate.bind(this));
    }

    stopAnimation() {
        this.isPlaying = false;
        if (this.animationId) {
            cancelAnimationFrame(this.animationId);
        }
    }

    animate(timestamp) {
        if (!this.isPlaying) return;

        this.drawFrame(timestamp);
        this.animationId = requestAnimationFrame(this.animate.bind(this));
    }

    drawFrame(timestamp) {
        const ctx = this.ctx;
        const width = this.canvas.width;
        const height = this.canvas.height;

        // Очищаем canvas
        ctx.fillStyle = '#1a1a1a';
        ctx.fillRect(0, 0, width, height);

        // Рисуем "камеру наблюдения" стиль
        this.drawCameraStyle(ctx, width, height, timestamp);
        
        // Добавляем информацию о камере
        this.drawCameraInfo(ctx, width, height);
        
        // Анимированные элементы
        this.drawAnimatedElements(ctx, width, height, timestamp);
    }

    drawCameraStyle(ctx, width, height, timestamp) {
        // Сетка как в настоящих камерах
        ctx.strokeStyle = 'rgba(0, 255, 0, 0.3)';
        ctx.lineWidth = 1;
        
        // Вертикальные линии
        for (let x = 0; x < width; x += 40) {
            ctx.beginPath();
            ctx.moveTo(x, 0);
            ctx.lineTo(x, height);
            ctx.stroke();
        }
        
        // Горизонтальные линии
        for (let y = 0; y < height; y += 40) {
            ctx.beginPath();
            ctx.moveTo(0, y);
            ctx.lineTo(width, y);
            ctx.stroke();
        }

        // Эффект "шума" как в аналоговых камерах
        this.drawNoise(ctx, width, height, timestamp);
    }

    drawNoise(ctx, width, height, timestamp) {
        const noiseIntensity = 2;
        const time = timestamp * 0.001;
        
        for (let i = 0; i < 50; i++) {
            const x = Math.random() * width;
            const y = Math.random() * height;
            const size = Math.random() * 2;
            const alpha = 0.1 + Math.sin(time + i) * 0.05;
            
            ctx.fillStyle = `rgba(255, 255, 255, ${alpha})`;
            ctx.fillRect(x, y, size, size);
        }
    }

    drawCameraInfo(ctx, width, height) {
        // Верхняя информационная панель
        ctx.fillStyle = 'rgba(0, 0, 0, 0.7)';
        ctx.fillRect(0, 0, width, 30);
        
        ctx.fillStyle = '#00ff00';
        ctx.font = '12px Arial';
        ctx.fillText(this.cameraName, 10, 20);
        ctx.fillText(this.location, width - 150, 20);
        
        // Нижняя панель с временем
        ctx.fillStyle = 'rgba(0, 0, 0, 0.7)';
        ctx.fillRect(0, height - 25, width, 25);
        
        const now = new Date();
        const timeString = now.toLocaleTimeString();
        const dateString = now.toLocaleDateString();
        
        ctx.fillStyle = '#00ff00';
        ctx.fillText(`${dateString} ${timeString}`, 10, height - 8);
        ctx.fillText('● REC', width - 50, height - 8);
    }

    drawAnimatedElements(ctx, width, height, timestamp) {
        const time = timestamp * 0.001;
        
        // Движущиеся объекты (имитация людей/машин)
        this.drawMovingObjects(ctx, width, height, time);
        
        // Мигающий статус
        this.drawBlinkingStatus(ctx, width, height, time);
        
        // Случайные события
        this.drawRandomEvents(ctx, width, height, time);
    }

    drawMovingObjects(ctx, width, height, time) {
        // Симуляция движения в разных камерах
        switch(this.cameraId) {
            case 'kpp':
                this.drawKPPActivity(ctx, width, height, time);
                break;
            case 'hall':
                this.drawHallActivity(ctx, width, height, time);
                break;
            case 'parking':
                this.drawParkingActivity(ctx, width, height, time);
                break;
            case 'warehouse':
                this.drawWarehouseActivity(ctx, width, height, time);
                break;
        }
    }

    drawKPPActivity(ctx, width, height, time) {
        // Движение на КПП - люди и машины
        ctx.fillStyle = '#ff4444';
        
        // Машина
        const carX = (time * 20) % (width + 100) - 50;
        if (carX < width) {
            ctx.fillRect(carX, height - 80, 60, 30);
            ctx.fillStyle = '#ffff00';
            ctx.fillRect(carX + 45, height - 75, 10, 5); // Фары
        }
        
        // Человек
        const personX = (time * 10) % (width + 50) - 25;
        if (personX < width && personX > 0) {
            ctx.fillStyle = '#4444ff';
            ctx.fillRect(personX, height - 120, 15, 40); // Тело
            ctx.fillRect(personX - 5, height - 125, 25, 10); // Голова
        }
    }

    drawHallActivity(ctx, width, height, time) {
        // Движение в холле - несколько людей
        ctx.fillStyle = '#4444ff';
        
        for (let i = 0; i < 3; i++) {
            const offset = i * 2;
            const personX = ((time * 8 + offset) * 30) % (width + 40) - 20;
            if (personX < width && personX > 0) {
                ctx.fillRect(personX, height - 100 - i * 30, 12, 30);
                ctx.fillRect(personX - 3, height - 105 - i * 30, 18, 8);
            }
        }
    }

    drawBlinkingStatus(ctx, width, height, time) {
        // Мигающий статус онлайн
        const blink = Math.sin(time * 5) > 0;
        ctx.fillStyle = blink ? '#00ff00' : '#004400';
        ctx.beginPath();
        ctx.arc(width - 20, 15, 5, 0, 2 * Math.PI);
        ctx.fill();
    }

    drawRandomEvents(ctx, width, height, time) {
        // Случайные события для реализма
        if (Math.random() < 0.002) { // 0.2% chance per frame
            ctx.fillStyle = 'rgba(255, 0, 0, 0.3)';
            ctx.fillRect(0, 0, width, height);
            
            ctx.fillStyle = '#ffffff';
            ctx.font = 'bold 24px Arial';
            ctx.textAlign = 'center';
            ctx.fillText('СОБЫТИЕ', width / 2, height / 2);
            ctx.textAlign = 'left';
        }
    }
}
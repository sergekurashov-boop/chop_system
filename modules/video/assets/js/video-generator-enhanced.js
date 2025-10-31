// modules/video/assets/js/video-generator-enhanced.js
class EnhancedVideoGenerator {
    constructor() {
        this.canvas = document.createElement('canvas');
        this.ctx = this.canvas.getContext('2d');
        this.canvas.width = 1280; // Full HD для реализма
        this.canvas.height = 720;
        this.isPlaying = false;
        this.animationId = null;
        this.scenarios = this.initScenarios();
    }

    initScenarios() {
        return {
            'kpp': {
                objects: [
                    { x: 200, y: 400, type: 'security_guard', speed: 0.8, direction: 1 },
                    { x: 600, y: 450, type: 'car_entering', speed: 1.2, direction: 1 },
                    { x: 100, y: 300, type: 'visitor', speed: 1, direction: -1 }
                ],
                background: 'entrance',
                timeOfDay: 'day'
            },
            'hall': {
                objects: [
                    { x: 300, y: 350, type: 'employee', speed: 1, direction: 1 },
                    { x: 500, y: 400, type: 'employee', speed: 0.7, direction: -1 },
                    { x: 100, y: 300, type: 'cleaner', speed: 0.5, direction: 1 }
                ],
                background: 'office',
                timeOfDay: 'day'
            },
            'parking': {
                objects: [
                    { x: 400, y: 450, type: 'car_parking', speed: 0.8, direction: -1 },
                    { x: 200, y: 380, type: 'driver', speed: 1, direction: 1 },
                    { x: 600, y: 420, type: 'car_leaving', speed: 1.5, direction: 1 }
                ],
                background: 'parking',
                timeOfDay: 'day'
            },
            'warehouse': {
                objects: [
                    { x: 350, y: 400, type: 'forklift', speed: 0.6, direction: 1 },
                    { x: 150, y: 350, type: 'worker', speed: 0.9, direction: -1 },
                    { x: 500, y: 380, type: 'worker', speed: 0.7, direction: 1 }
                ],
                background: 'warehouse',
                timeOfDay: 'indoor'
            }
        };
    }

    generateCameraFeed(cameraId, cameraName, location) {
        this.currentScenario = this.scenarios[cameraId] || this.scenarios['kpp'];
        this.cameraId = cameraId;
        this.cameraName = cameraName;
        this.location = location;
        
        this.startAnimation();
        return this.canvas.captureStream(30); // 30 FPS для плавности
    }

    drawFrame(timestamp) {
        const ctx = this.ctx;
        const width = this.canvas.width;
        const height = this.canvas.height;
        const time = timestamp * 0.001;

        // Очищаем с fade эффектом
        ctx.fillStyle = 'rgba(20, 20, 30, 0.1)';
        ctx.fillRect(0, 0, width, height);

        // Рисуем фон в зависимости от сценария
        this.drawBackground(ctx, width, height, time);
        
        // Обновляем и рисуем объекты
        this.updateObjects(time);
        this.drawObjects(ctx, time);
        
        // Эффекты камеры наблюдения
        this.drawCameraEffects(ctx, width, height, time);
        
        // Информация о камере
        this.drawCameraInfo(ctx, width, height);
    }

    drawBackground(ctx, width, height, time) {
        const scenario = this.currentScenario;
        
        switch(scenario.background) {
            case 'entrance':
                this.drawEntranceBackground(ctx, width, height, time);
                break;
            case 'office':
                this.drawOfficeBackground(ctx, width, height, time);
                break;
            case 'parking':
                this.drawParkingBackground(ctx, width, height, time);
                break;
            case 'warehouse':
                this.drawWarehouseBackground(ctx, width, height, time);
                break;
        }
    }

    drawEntranceBackground(ctx, width, height, time) {
        // Фон КПП - шлагбаум, будка охраны
        ctx.fillStyle = '#2a4a2a';
        ctx.fillRect(0, height - 100, width, 100); // Земля
        
        ctx.fillStyle = '#8b4513';
        ctx.fillRect(width/2 - 200, height - 150, 400, 50); // Будка охраны
        
        ctx.fillStyle = '#654321';
        ctx.fillRect(width/2 - 300, height - 120, 600, 20); // Шлагбаум
        
        // Разметка
        ctx.strokeStyle = 'white';
        ctx.lineWidth = 3;
        ctx.setLineDash([20, 15]);
        ctx.beginPath();
        ctx.moveTo(0, height - 50);
        ctx.lineTo(width, height - 50);
        ctx.stroke();
        ctx.setLineDash([]);
    }

    drawOfficeBackground(ctx, width, height, time) {
        // Офисный холл
        ctx.fillStyle = '#f5f5f5';
        ctx.fillRect(0, 0, width, height);
        
        // Стены
        ctx.fillStyle = '#e8e8e8';
        ctx.fillRect(0, 0, width, 80);
        ctx.fillRect(0, height - 60, width, 60);
        
        // Двери
        ctx.fillStyle = '#8b4513';
        ctx.fillRect(100, height - 160, 40, 100);
        ctx.fillRect(width - 140, height - 160, 40, 100);
        
        // Освещение
        ctx.fillStyle = 'rgba(255, 255, 200, 0.3)';
        for (let i = 0; i < width; i += 200) {
            ctx.beginPath();
            ctx.arc(i + 100, 40, 30, 0, Math.PI * 2);
            ctx.fill();
        }
    }

    // ... аналогичные методы для других фонов ...

    drawObjects(ctx, time) {
        this.currentScenario.objects.forEach(obj => {
            ctx.save();
            
            switch(obj.type) {
                case 'security_guard':
                    this.drawSecurityGuard(ctx, obj.x, obj.y, obj.direction, time);
                    break;
                case 'car_entering':
                    this.drawCar(ctx, obj.x, obj.y, obj.direction, time, '#ff4444');
                    break;
                case 'visitor':
                    this.drawPerson(ctx, obj.x, obj.y, obj.direction, time, '#44ff44');
                    break;
                case 'employee':
                    this.drawPerson(ctx, obj.x, obj.y, obj.direction, time, '#4444ff');
                    break;
                case 'forklift':
                    this.drawForklift(ctx, obj.x, obj.y, obj.direction, time);
                    break;
                // ... другие типы объектов
            }
            
            ctx.restore();
        });
    }

    drawSecurityGuard(ctx, x, y, direction, time) {
        // Форма охраны
        ctx.fillStyle = '#2c3e50';
        ctx.fillRect(x, y - 45, 20 * direction, 45);
        
        // Желтые полосы
        ctx.fillStyle = '#f1c40f';
        ctx.fillRect(x, y - 35, 20 * direction, 5);
        ctx.fillRect(x, y - 20, 20 * direction, 5);
        
        // Голова
        ctx.fillStyle = '#ffdbac';
        ctx.fillRect(x - 5 * direction, y - 55, 30 * direction, 10);
        
        // Фуражка
        ctx.fillStyle = '#2c3e50';
        ctx.fillRect(x - 8 * direction, y - 60, 36 * direction, 5);
    }

    drawForklift(ctx, x, y, direction, time) {
        // Вилочный погрузчик
        ctx.fillStyle = '#e74c3c';
        ctx.fillRect(x, y - 25, 60 * direction, 20);
        
        // Кабина
        ctx.fillStyle = '#3498db';
        ctx.fillRect(x + 40 * direction, y - 40, 20 * direction, 15);
        
        // Вилы
        ctx.fillStyle = '#7f8c8d';
        ctx.fillRect(x - 20 * direction, y - 15, 20 * direction, 5);
        ctx.fillRect(x - 20 * direction, y - 25, 20 * direction, 5);
        
        // Колеса
        ctx.fillStyle = '#2c3e50';
        ctx.fillRect(x + 10 * direction, y - 5, 15, 8);
        ctx.fillRect(x + 35 * direction, y - 5, 15, 8);
    }

    drawCameraEffects(ctx, width, height, time) {
        // Более реалистичные эффекты камеры наблюдения
        this.drawDigitalNoise(ctx, width, height, time);
        this.drawTimestamp(ctx, width, height);
        this.drawCameraOverlay(ctx, width, height);
    }

    drawDigitalNoise(ctx, width, height, time) {
        // Цифровой шум как в IP-камерах
        const noiseData = ctx.createImageData(width, height);
        
        for (let i = 0; i < noiseData.data.length; i += 4) {
            if (Math.random() < 0.01) { // 1% пикселей - шум
                noiseData.data[i] = Math.random() * 255;     // R
                noiseData.data[i + 1] = Math.random() * 255; // G  
                noiseData.data[i + 2] = Math.random() * 255; // B
                noiseData.data[i + 3] = 50; // Alpha
            }
        }
        
        ctx.putImageData(noiseData, 0, 0);
    }

    drawTimestamp(ctx, width, height) {
        const now = new Date();
        const timeString = now.toLocaleTimeString('ru-RU');
        const dateString = now.toLocaleDateString('ru-RU');
        
        ctx.fillStyle = 'rgba(0, 0, 0, 0.7)';
        ctx.fillRect(width - 200, height - 25, 200, 25);
        
        ctx.fillStyle = '#00ff00';
        ctx.font = '14px "Courier New", monospace';
        ctx.fillText(`${dateString} ${timeString}`, width - 190, height - 8);
    }

    // ... остальные методы как в предыдущем генераторе ...
}
// modules/video/assets/js/video-generator.js
class VideoGenerator {
    constructor() {
        this.canvas = document.createElement('canvas');
        this.ctx = this.canvas.getContext('2d');
        this.canvas.width = 640;
        this.canvas.height = 480;
        this.isPlaying = false;
        this.animationId = null;
        this.objects = []; // Для хранения движущихся объектов
        this.events = []; // Для хранения событий
        this.initObjects();
    }

    initObjects() {
        // Инициализируем начальные объекты для каждой камеры
        this.objects = [
            { x: 100, y: 300, type: 'person', speed: 1.5, direction: 1 },
            { x: 400, y: 350, type: 'car', speed: 2, direction: -1 },
            { x: 200, y: 200, type: 'person', speed: 1, direction: 1 }
        ];
    }

    generateCameraFeed(cameraId, cameraName, location) {
        this.cameraId = cameraId;
        this.cameraName = cameraName;
        this.location = location;
        
        this.startAnimation();
        return this.canvas.captureStream(25);
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

        this.updateObjects(timestamp);
        this.drawFrame(timestamp);
        this.animationId = requestAnimationFrame(this.animate.bind(this));
    }

    updateObjects(timestamp) {
        const time = timestamp * 0.001;
        
        // Обновляем позиции объектов
        this.objects.forEach(obj => {
            obj.x += obj.speed * obj.direction;
            
            // Если объект ушел за границы, возвращаем его
            if (obj.x > this.canvas.width + 50) {
                obj.x = -50;
                // Случайное событие при появлении объекта
                if (Math.random() < 0.3) {
                    this.createEvent(obj.type + '_appeared', obj.x, obj.y);
                }
            }
            if (obj.x < -50) {
                obj.x = this.canvas.width + 50;
            }
            
            // Случайное изменение направления
            if (Math.random() < 0.005) {
                obj.direction *= -1;
            }
        });

        // Случайное создание новых объектов
        if (Math.random() < 0.01 && this.objects.length < 6) {
            this.createRandomObject();
        }

        // Обновляем события (время жизни)
        this.events = this.events.filter(event => time - event.created < 3);
    }

    createRandomObject() {
        const types = ['person', 'car', 'person'];
        const type = types[Math.floor(Math.random() * types.length)];
        
        this.objects.push({
            x: Math.random() > 0.5 ? -50 : this.canvas.width + 50,
            y: 300 + Math.random() * 100,
            type: type,
            speed: 0.5 + Math.random() * 2,
            direction: Math.random() > 0.5 ? 1 : -1
        });
    }

    createEvent(type, x, y) {
        this.events.push({
            type: type,
            x: x,
            y: y,
            created: performance.now() * 0.001
        });
    }

    drawFrame(timestamp) {
        const ctx = this.ctx;
        const width = this.canvas.width;
        const height = this.canvas.height;
        const time = timestamp * 0.001;

        // Очищаем canvas с легким fade эффектом
        ctx.fillStyle = 'rgba(26, 26, 26, 0.1)';
        ctx.fillRect(0, 0, width, height);

        // Рисуем "камеру наблюдения" стиль
        this.drawCameraStyle(ctx, width, height, time);
        
        // Рисуем движущиеся объекты
        this.drawObjects(ctx, time);
        
        // Рисуем события
        this.drawEvents(ctx, time);
        
        // Добавляем информацию о камере
        this.drawCameraInfo(ctx, width, height);
        
        // Анимированные элементы
        this.drawAnimatedElements(ctx, width, height, time);
    }

    drawCameraStyle(ctx, width, height, timestamp) {
        // Сетка как в настоящих камерах (анимированная)
        ctx.strokeStyle = `rgba(0, 255, 0, ${0.2 + Math.sin(timestamp * 2) * 0.1})`;
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

        // Эффект мерцания
        if (Math.random() < 0.02) {
            ctx.fillStyle = 'rgba(255, 255, 255, 0.1)';
            ctx.fillRect(0, 0, width, height);
        }
    }

    drawNoise(ctx, width, height, timestamp) {
        const time = timestamp * 0.001;
        const noiseCount = 30 + Math.sin(time) * 10; // Колеблющееся количество шума
        
        for (let i = 0; i < noiseCount; i++) {
            const x = Math.random() * width;
            const y = Math.random() * height;
            const size = Math.random() * 3;
            const alpha = 0.05 + Math.sin(time + i) * 0.05;
            
            ctx.fillStyle = `rgba(255, 255, 255, ${alpha})`;
            ctx.fillRect(x, y, size, size);
        }
    }

    drawObjects(ctx, time) {
        this.objects.forEach(obj => {
            ctx.save();
            
            switch(obj.type) {
                case 'person':
                    this.drawPerson(ctx, obj.x, obj.y, obj.direction, time);
                    break;
                case 'car':
                    this.drawCar(ctx, obj.x, obj.y, obj.direction, time);
                    break;
            }
            
            ctx.restore();
        });
    }

    drawPerson(ctx, x, y, direction, time) {
        // Анимированная ходьба
        const walkOffset = Math.sin(time * 10) * 3;
        
        ctx.fillStyle = '#4444ff';
        // Тело
        ctx.fillRect(x, y - 40 + walkOffset, 15 * direction, 40);
        // Голова
        ctx.fillRect(x - 5 * direction, y - 50 + walkOffset, 25 * direction, 10);
        
        // Тень
        ctx.fillStyle = 'rgba(0, 0, 0, 0.3)';
        ctx.fillRect(x, y, 15, 5);
    }

    drawCar(ctx, x, y, direction, time) {
        ctx.fillStyle = '#ff4444';
        // Кузов
        ctx.fillRect(x, y - 30, 60 * direction, 20);
        // Кабина
        ctx.fillRect(x + 40 * direction, y - 40, 20 * direction, 10);
        
        // Колеса
        ctx.fillStyle = '#333';
        ctx.fillRect(x + 10 * direction, y - 10, 10, 5);
        ctx.fillRect(x + 40 * direction, y - 10, 10, 5);
        
        // Фары (мигающие)
        ctx.fillStyle = Math.sin(time * 5) > 0 ? '#ffff00' : '#ffaa00';
        if (direction > 0) {
            ctx.fillRect(x + 55, y - 25, 5, 3);
        } else {
            ctx.fillRect(x, y - 25, 5, 3);
        }
        
        // Тень
        ctx.fillStyle = 'rgba(0, 0, 0, 0.3)';
        ctx.fillRect(x, y, 60, 5);
    }

    drawEvents(ctx, time) {
        this.events.forEach(event => {
            const age = time - event.created;
            const alpha = 1 - (age / 3); // Fade out за 3 секунды
            
            ctx.save();
            ctx.globalAlpha = alpha;
            
            ctx.fillStyle = '#ff4444';
            ctx.beginPath();
            ctx.arc(event.x, event.y, 10, 0, 2 * Math.PI);
            ctx.fill();
            
            ctx.strokeStyle = '#ffff00';
            ctx.lineWidth = 2;
            ctx.beginPath();
            ctx.arc(event.x, event.y, 15 + age * 5, 0, 2 * Math.PI);
            ctx.stroke();
            
            ctx.restore();
        });
    }

    drawCameraInfo(ctx, width, height) {
        // Верхняя информационная панель
        ctx.fillStyle = 'rgba(0, 0, 0, 0.8)';
        ctx.fillRect(0, 0, width, 30);
        
        ctx.fillStyle = '#00ff00';
        ctx.font = '12px Arial';
        ctx.fillText(this.cameraName, 10, 20);
        ctx.fillText(this.location, width - 150, 20);
        
        // Нижняя панель с временем
        ctx.fillStyle = 'rgba(0, 0, 0, 0.8)';
        ctx.fillRect(0, height - 25, width, 25);
        
        const now = new Date();
        const timeString = now.toLocaleTimeString();
        const dateString = now.toLocaleDateString();
        
        ctx.fillStyle = '#00ff00';
        ctx.fillText(`${dateString} ${timeString}`, 10, height - 8);
        ctx.fillText('● REC', width - 50, height - 8);
    }

    drawAnimatedElements(ctx, width, height, time) {
        // Мигающий статус онлайн
        const blink = Math.sin(time * 5) > 0;
        ctx.fillStyle = blink ? '#00ff00' : '#004400';
        ctx.beginPath();
        ctx.arc(width - 20, 15, 5, 0, 2 * Math.PI);
        ctx.fill();

        // Анимированный индикатор записи
        const recBlink = Math.sin(time * 8) > 0;
        ctx.fillStyle = recBlink ? '#ff0000' : '#440000';
        ctx.beginPath();
        ctx.arc(width - 20, height - 15, 4, 0, 2 * Math.PI);
        ctx.fill();

        // Случайные события для реализма
        if (Math.random() < 0.001) { // 0.1% chance per frame
            this.createRandomEvent();
        }
    }

    createRandomEvent() {
        const eventTypes = ['motion', 'object_detected', 'crossing_line'];
        const type = eventTypes[Math.floor(Math.random() * eventTypes.length)];
        const x = 100 + Math.random() * (this.canvas.width - 200);
        const y = 100 + Math.random() * (this.canvas.height - 200);
        
        this.createEvent(type, x, y);
    }
}
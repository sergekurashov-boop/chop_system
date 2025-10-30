// ЕДИНЫЙ обработчик DOMContentLoaded
document.addEventListener('DOMContentLoaded', function() {
    // 1. Основные функции системы
    initTooltips();
    initForms();
    initTables();
    
    // 2. Сайдбар аккордеон с сохранением состояния модуля
    const navHeaders = document.querySelectorAll('.nav-header');
    navHeaders.forEach(header => {
        header.addEventListener('click', function() {
            const section = this.parentElement;
            section.classList.toggle('active');
        });
    });
    
    // 3. Автоматическое открытие активного модуля
    initActiveModule();
    
    // 4. Мобильное меню
    const mobileToggle = document.querySelector('.mobile-menu-toggle');
    const sidebar = document.querySelector('.sidebar');
    const mainContent = document.querySelector('.main-content');
    
    if (mobileToggle) {
        mobileToggle.addEventListener('click', function() {
            sidebar.classList.toggle('open');
            mainContent.classList.toggle('sidebar-open');
        });
    }
    
    // 5. Закрытие сайдбара при клике на ссылку (мобильные)
    const navLinks = document.querySelectorAll('.nav-link');
    navLinks.forEach(link => {
        link.addEventListener('click', function() {
            if (window.innerWidth <= 768) {
                sidebar.classList.remove('open');
                mainContent.classList.remove('sidebar-open');
            }
            
            // Сохраняем активный модуль в sessionStorage
            const module = detectCurrentModule(this.href);
            if (module) {
                sessionStorage.setItem('activeModule', module);
            }
        });
    });
});

// Функция для определения текущего модуля по URL
function detectCurrentModule(url = null) {
    const currentUrl = url || window.location.href;
    
    // Определяем модуль по пути
    if (currentUrl.includes('/shifts') || currentUrl.includes('/смены')) {
        return 'shifts';
    } else if (currentUrl.includes('/users') || currentUrl.includes('/пользователи')) {
        return 'users';
    } else if (currentUrl.includes('/objects') || currentUrl.includes('/объекты')) {
        return 'objects';
    } else if (currentUrl.includes('/instructions') || currentUrl.includes('/инструкции')) {
        return 'instructions';
    } else if (currentUrl.includes('/video') || currentUrl.includes('/видео')) {
        return 'video';
    } else if (currentUrl.includes('/admin') || currentUrl.includes('/админ')) {
        return 'admin';
    } else if (currentUrl.includes('/reports') || currentUrl.includes('/отчеты')) {
        return 'reports';
    } else if (currentUrl.includes('/medic') || currentUrl.includes('/медицина')) {
        return 'medic';
    }
    
    return null;
}

// Функция инициализации активного модуля
function initActiveModule() {
    const currentModule = detectCurrentModule();
    const savedModule = sessionStorage.getItem('activeModule');
    
    // Приоритет текущей страницы над сохраненным состоянием
    const activeModule = currentModule || savedModule;
    
    if (activeModule) {
        // Закрываем все секции
        document.querySelectorAll('.nav-section').forEach(section => {
            section.classList.remove('active');
        });
        
        // Открываем нужную секцию
        const targetSection = document.querySelector(`[data-module="${activeModule}"]`);
        if (targetSection) {
            targetSection.classList.add('active');
        } else {
            // Если data-module не установлен, ищем по содержимому
            openModuleByContent(activeModule);
        }
        
        // Сохраняем текущий модуль
        sessionStorage.setItem('activeModule', activeModule);
    }
}

// Функция открытия модуля по содержимому ссылок
function openModuleByContent(module) {
    const navSections = document.querySelectorAll('.nav-section');
    
    navSections.forEach(section => {
        const links = section.querySelectorAll('.nav-link');
        let hasModuleLink = false;
        
        links.forEach(link => {
            const linkModule = detectCurrentModule(link.href);
            if (linkModule === module) {
                hasModuleLink = true;
            }
        });
        
        if (hasModuleLink) {
            section.classList.add('active');
        }
    });
}

// Функции для тултипов
function initTooltips() {
    const tooltips = document.querySelectorAll('[data-tooltip]');
    tooltips.forEach(element => {
        element.addEventListener('mouseenter', showTooltip);
        element.addEventListener('mouseleave', hideTooltip);
    });
}

function showTooltip(e) {
    const tooltipText = e.target.getAttribute('data-tooltip');
    const tooltip = document.createElement('div');
    tooltip.className = 'tooltip';
    tooltip.textContent = tooltipText;
    tooltip.style.cssText = `
        position: absolute;
        background: #333;
        color: white;
        padding: 5px 10px;
        border-radius: 4px;
        font-size: 12px;
        z-index: 1000;
    `;
    
    document.body.appendChild(tooltip);
    
    const rect = e.target.getBoundingClientRect();
    tooltip.style.left = rect.left + 'px';
    tooltip.style.top = (rect.top - tooltip.offsetHeight - 5) + 'px';
    
    e.target._tooltip = tooltip;
}

function hideTooltip(e) {
    if (e.target._tooltip) {
        e.target._tooltip.remove();
        e.target._tooltip = null;
    }
}

// Функции для форм
function initForms() {
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const required = this.querySelectorAll('[required]');
            let valid = true;
            
            required.forEach(field => {
                if (!field.value.trim()) {
                    valid = false;
                    field.style.borderColor = 'var(--danger-gray)';
                } else {
                    field.style.borderColor = '';
                }
            });
            
            if (!valid) {
                e.preventDefault();
                alert('Пожалуйста, заполните все обязательные поля');
            }
        });
    });
}

// Функции для таблиц
function initTables() {
    const tables = document.querySelectorAll('.table');
    tables.forEach(table => {
        const headers = table.querySelectorAll('th[data-sort]');
        headers.forEach(header => {
            header.style.cursor = 'pointer';
            header.addEventListener('click', function() {
                sortTable(table, this.cellIndex, this.getAttribute('data-sort'));
            });
        });
    });
}

function sortTable(table, column, type) {
    const tbody = table.querySelector('tbody');
    const rows = Array.from(tbody.querySelectorAll('tr'));
    
    rows.sort((a, b) => {
        const aVal = a.cells[column].textContent;
        const bVal = b.cells[column].textContent;
        
        if (type === 'numeric') {
            return parseFloat(aVal) - parseFloat(bVal);
        } else if (type === 'date') {
            return new Date(aVal) - new Date(bVal);
        } else {
            return aVal.localeCompare(bVal);
        }
    });
    
    // Удаляем существующие строки
    while (tbody.firstChild) {
        tbody.removeChild(tbody.firstChild);
    }
    
    // Добавляем отсортированные строки
    rows.forEach(row => tbody.appendChild(row));
}

// Функции для работы с модальными окнами
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = 'block';
        document.body.style.overflow = 'hidden';
    }
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
    }
}

// Функция печати
function printElement(elementId) {
    const element = document.getElementById(elementId);
    if (element) {
        const printWindow = window.open('', '_blank');
        printWindow.document.write(`
            <html>
                <head>
                    <title>Печать</title>
                    <style>
                        body { font-family: Arial, sans-serif; margin: 20px; }
                        table { width: 100%; border-collapse: collapse; }
                        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                        th { background-color: #f2f2f2; }
                        .no-print { display: none; }
                    </style>
                </head>
                <body>
                    ${element.innerHTML}
                </body>
            </html>
        `);
        printWindow.document.close();
        printWindow.print();
    }
}

// Добавьте этот код в ваш основной JS-файл
document.getElementById('sidebarToggle').addEventListener('click', function() {
    const sidebar = document.querySelector('.sidebar');
    sidebar.classList.toggle('collapsed');
    
    // Опционально: Сохраняем состояние в LocalStorage
    const isCollapsed = sidebar.classList.contains('collapsed');
    localStorage.setItem('sidebarCollapsed', isCollapsed);
});

// При загрузке страницы восстанавливаем состояние
document.addEventListener('DOMContentLoaded', function() {
    const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
    const sidebar = document.querySelector('.sidebar');
    if (isCollapsed) {
        sidebar.classList.add('collapsed');
    }
});

// AJAX функции
async function apiCall(url, method = 'GET', data = null) {
    try {
        const options = {
            method: method,
            headers: {
                'Content-Type': 'application/json',
            }
        };
        
        if (data) {
            options.body = JSON.stringify(data);
        }
        
        const response = await fetch(url, options);
        return await response.json();
    } catch (error) {
        console.error('API call error:', error);
        return { success: false, error: error.message };
    }
}

// Глобальные функции для вызова из HTML
window.openModal = openModal;
window.closeModal = closeModal;
window.printElement = printElement;
window.apiCall = apiCall;
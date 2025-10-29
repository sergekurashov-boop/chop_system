// instructions.js - весь скрипт как был, просто вынесли
document.addEventListener('DOMContentLoaded', function() {
    // Инициализация CKEditor
    if (typeof CKEDITOR !== 'undefined') {
        CKEDITOR.replace('instruction_content', {
            language: 'ru',
            height: 300,
            toolbar: [
                ['Bold', 'Italic', 'Underline', 'Strike'],
                ['NumberedList', 'BulletedList'],
                ['Link', 'Unlink'],
                ['Table'],
                ['Source']
            ]
        });
    }

    // Функция редактирования инструкции (оставляем глобальной)
    window.editInstruction = function(id, title, category, content) {
        document.getElementById('instruction_id').value = id;
        document.getElementById('instruction_title').value = title;
        document.querySelector('select[name="category"]').value = category;
        
        if (CKEDITOR.instances.instruction_content) {
            CKEDITOR.instances.instruction_content.setData(content);
        }
        
        document.getElementById('cancelEdit').style.display = 'inline-block';
        document.querySelector('button[name="save_instruction"]').textContent = 'Обновить инструкцию';
        document.getElementById('instructionForm').scrollIntoView({ behavior: 'smooth' });
    };

    // Отмена редактирования
    const cancelBtn = document.getElementById('cancelEdit');
    if (cancelBtn) {
        cancelBtn.addEventListener('click', function() {
            document.getElementById('instruction_id').value = '';
            document.getElementById('instruction_title').value = '';
            document.querySelector('select[name="category"]').value = '';
            
            if (CKEDITOR.instances.instruction_content) {
                CKEDITOR.instances.instruction_content.setData('');
            }
            
            this.style.display = 'none';
            document.querySelector('button[name="save_instruction"]').textContent = 'Сохранить инструкцию';
        });
    }
});
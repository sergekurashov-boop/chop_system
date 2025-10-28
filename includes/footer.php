<?php if (!defined('FOOTER_INCLUDED')): ?>
<?php define('FOOTER_INCLUDED', true); ?>
</div>

<footer style="background: var(--dark-gray); color: white; padding: 2rem 0; margin-top: 3rem;">
    <div class="container">
        <div class="row">
            <div class="col-4">
                <h4>Система управления ЧОП</h4>
                <p>Версия 1.0</p>
            </div>
            <div class="col-4">
                <h4>Контакты</h4>
                <p>Техническая поддержка: support@chop-system.ru</p>
            </div>
            <div class="col-4">
                <h4>Статус системы</h4>
                <p>Все системы работают в штатном режиме</p>
            </div>
        </div>
        <div style="text-align: center; margin-top: 2rem; padding-top: 1rem; border-top: 1px solid rgba(255,255,255,0.1);">
            &copy; <?php echo date('Y'); ?> Система управления ЧОП. Все права защищены.
        </div>
    </div>
</footer>

<script src="assets/js/script.js"></script>
</body>
</html>
<?php endif; ?>
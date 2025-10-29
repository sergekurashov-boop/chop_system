<?php if (!defined('FOOTER_INCLUDED')): ?>
<?php define('FOOTER_INCLUDED', true); ?>
</div>

<footer style="background: #808080; color: white; padding: 2rem 0; margin-top: 3rem;">
    <div class="container">
        <div class="row" >
            <div class="col-4"style="margin-left: auto;">
                <h4>Версия 1.0</h4>
                
            </div>
            <div class="col-4"style="margin-left: auto;">
                <h4>Контакты</h4>
                <p>Техническая поддержка:  <a href="mailto:spk188@mail.ru" style="color: #f8f9fa; text-decoration: none;">✉️ spk188@mail.ru</a></p>
            </div>
            <div class="col-4"style="margin-left: auto;">
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
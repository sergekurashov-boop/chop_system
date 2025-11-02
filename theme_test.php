<?php
session_start();

if (isset($_GET['theme'])) {
    $_SESSION['theme'] = $_GET['theme'];
    header("Location: index.php");
    exit;
}

$theme = $_SESSION['theme'] ?? 'default';
?>

<div style="position: fixed; top: 10px; right: 10px; z-index: 10000; background: white; padding: 10px; border-radius: 5px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
    <strong>Тема:</strong>
    <select onchange="location.href='theme_test.php?theme=' + this.value">
        <option value="default" <?php echo $theme === 'default' ? 'selected' : ''; ?>>Стандартная</option>
        <option value="pro" <?php echo $theme === 'pro' ? 'selected' : ''; ?>>Синяя Pro</option>
        <option value="gold" <?php echo $theme === 'gold' ? 'selected' : ''; ?>>Золотая</option>
    </select>
</div>
<?php
function flash_set($type, $message) {
    $_SESSION['flash'] = ['type'=>$type, 'message'=>$message];
}

function flash_get() {
    if (!empty($_SESSION['flash'])) {
        $f = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $f;
    }
    return null;
}

function render_flash() {
    $f = flash_get();
    if (!$f) return;

    $class = $f['type'] === 'success' ? 'flash success' : 'flash error';
    echo "<div class='$class'>".htmlspecialchars($f['message'])."</div>";
}

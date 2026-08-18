<?php
session_start();
$title = "DiloFlow — Простий менеджер задач";

require_once __DIR__ . '/includes/header.php';
?>
<main class="container my-5 py-5 text-center flex-grow-1">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h1 class="display-4 fw-bold text-dark fst-italic mb-4">Керуй своїми завданнями без зусиль</h1>
            <p class="lead text-secondary mb-5">
                DiloFlow — це простий, швидкий та зручний менеджер задач, який допоможе тобі тримати все під контролем та підвищити власну продуктивність.
            </p>
            <div class="d-flex justify-content-center gap-3 align-items-start flex-wrap">
                <div class="d-flex flex-column gap-2">
                    <?php if(isset($_SESSION['user'])):?>
                        <a href="<?= BASE_URL ?>/pages/lists.php" class="btn btn-success btn-lg px-4">Кабінет</a>
                    <?php else :?>
                        <a href="<?= BASE_URL ?>/auth/login.php" class="btn btn-success btn-lg px-4">Почати безкоштовно</a>
                    <?php endif; ?>
                    
                    <button id="installAppBtn" style="display: none;" class="btn btn-outline-success btn-md px-3">
                        📲 Встановити на телефон
                    </button>
                </div>
                <a href="<?= BASE_URL ?>/pages/about.php" class="btn btn-outline-secondary btn-lg px-4">Дізнатися більше</a>
            </div>

            <div class="mt-4 text-muted small">
                <span>💡 Ви можете встановити застосунок собі на екран смартфона. Якщо кнопки «Встановити» немає вище — </span>
                <a href="<?= BASE_URL ?>/pages/install-guide.php" class="text-primary text-decoration-underline fw-semibold">
                    перегляньте коротку інструкцію для вашого браузера
                </a>
            </div>
        </div>
    </div>
</main>
<?php
require_once __DIR__ . '/includes/footer.php';
?>
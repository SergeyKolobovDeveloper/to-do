<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../includes/header.php';
?>
<main>
    <div class="container my-5">
        <div class="row mb-5">
            <div class="col-md-8 offset-md-2 text-center">
                <h1 class="display-4 fw-bold text-primary">Про проєкт DiloFlow</h1>
                <p class="lead text-muted">
                    DiloFlow — це сучасний, легкий та зручний таск-менеджер для ефективного планування завдань і досягнення цілей.
                </p>
            </div>
        </div>

        <div class="row">
            <div class="col-md-10 offset-md-1">
                <h3 class="mb-4">📜 Історія оновлень (Changelog)</h3>
                
                <div class="card mb-3 shadow-sm border-0">
                    <div class="card-body">
                        <p class="card-text text-muted">Перший публічний запуск проєкту DiloFlow у мережу!</p>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h5 class="card-title m-0 fw-bold">Версія 1.0 — Офіційний реліз</h5>
                            <span class="badge bg-success">27 Липня 2026</span>
                        </div>
                        <ul>
                            <li>Базовий CRUD для задач (створення, читання, оновлення, видалення)</li>
                            <li>Повноцінна реєстрація, авторизація</li>
                            <li>Адаптивний дизайн на Bootstrap</li>
                        </ul>
                        <hr class="my-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h5 class="card-title m-0 fw-bold">Версія 1.1</h5>
                            <span class="badge bg-success">31 Липня 2026</span>
                        </div>
                        <ul>
                            <li>Додано можливість відновлення пароля</li>
                            <li>Фільтрація задач: Всі / Активні / Виконані</li>
                        </ul>
                        <hr class="my-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h5 class="card-title m-0 fw-bold">Версія 1.2</h5>
                            <span class="badge bg-success">10 Серпня 2026</span>
                        </div>
                        <ul>
                            <li>Встановлення дедлайнів та сортування за датою.</li>
                            <li>Підтримка PWA (можливість встановити як додаток на телефон)</li>
                        </ul>
                        <hr class="my-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h5 class="card-title m-0 fw-bold">Версія 1.3</h5>
                            <span class="badge bg-success">17 Серпня 2026</span>
                        </div>
                        <ul>
                            <li>Додано сторінку "Про проект"</li>
                            <li>Додано сторінку "Зворотній зв'язок", щоб мати зв'язок для ваши ідей та якщо виникнуть проблеми</li>
                        </ul>
                    </div>
                </div>

                <div class="text-center mt-5 p-4 bg-light rounded-3">
                    <h4>Маєте ідеї щодо розвитку DiloFlow?</h4>
                    <p class="text-muted">Ми постійно працюємо над покращенням сервісу та ділимося новинами.</p>
                    <a href="<?= BASE_URL ?>/pages/feedback.php" class="btn btn-outline-success">Залишити відгук або пропозицію</a>
                </div>

            </div>
        </div>
    </div>
</main>
<?php
require_once __DIR__ . '/../includes/footer.php';
?>
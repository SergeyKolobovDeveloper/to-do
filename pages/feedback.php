<?php
$title = "Feedback";
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../includes/header.php';
?>

<main>
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <h1 class="display-4 fw-bold text-primary text-center mb-3">Зворотний зв'язок</h1>
                <p class="text-center text-muted mb-4">
                    Маєте ідеї щодо покращення DiloFlow або знайшли помилку? Напишіть нам!
                </p>

                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?= htmlspecialchars($_SESSION['success']) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php unset($_SESSION['success']); ?>
                <?php endif; ?>

                <?php if (isset($_SESSION['errors'])): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach ($_SESSION['errors'] as $error): ?>
                                <li><?= htmlspecialchars($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php unset($_SESSION['errors']); ?>
                <?php endif; ?>

                <div class="card shadow-sm border-0 p-4">
                    <form action="<?= BASE_URL ?>/actions/send-feedback.php" method="POST">
                        <div class="mb-3">
                            <label for="name" class="form-label">Введіть ваше ім'я</label>
                            <input type="text" id="name" name="name" class="form-control" placeholder="Введіть ваше ім'я" required>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Введіть ваш Email</label>
                            <input type="email" id="email" name="email" class="form-control" placeholder="Введіть вашу електронну пошту" required>
                        </div>

                        <div class="mb-3">
                            <label for="subject" class="form-label">Тип звернення</label>
                            <select id="subject" name="subject" class="form-select">
                                <option value="idea">Пропозиція / Ідея</option>
                                <option value="bug">Повідомити про баг</option>
                                <option value="other" selected>Загальне запитання</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="message" class="form-label">Повідомлення</label>
                            <textarea id="message" name="message" class="form-control" rows="4" placeholder="Опишіть вашу ідею або проблему..." required></textarea>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-success">Надіслати</button>
                            <a href="<?= BASE_URL ?>/index.php" class="btn btn-secondary">Назад на головну</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

<?php
require_once __DIR__ . '/../includes/footer.php';
?>
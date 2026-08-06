<?php
session_start();
$title = "Інструкція зі встановлення — DiloFlow";

require_once __DIR__ . '/../includes/header.php';
?>

<main class="container my-5 py-4 flex-grow-1">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <!-- Кнопка повернення на головну -->
            <div class="mb-4">
                <a href="<?= BASE_URL ?>/index.php" class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-arrow-left me-1"></i> Повернутися на головну
                </a>
            </div>

            <h1 class="h2 fw-bold mb-3 text-dark">Як встановити DiloFlow на смартфон</h1>
            <p class="text-secondary mb-4">
                Якщо у вас не відображається кнопка швидкого встановлення, скористайтеся інструкцією для вашого браузера:
            </p>

            <div class="accordion shadow-sm" id="installGuideAccordion">
                
                <!-- Safari (iPhone / iPad) -->
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button fw-bold text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#safariGuide">
                            🍎 Safari (iPhone / iPad)
                        </button>
                    </h2>
                    <div id="safariGuide" class="accordion-collapse collapse show" data-bs-parent="#installGuideAccordion">
                        <div class="accordion-body">
                            <ol class="mb-0">
                                <li class="mb-2">Відкрийте сайт <strong>DiloFlow</strong> у браузері Safari.</li>
                                <li class="mb-2">Натисніть кнопку <strong>«Поділитися»</strong> (квадрат зі стрілкою вгору внизу екрана).</li>
                                <li class="mb-2">Прокрутіть меню вниз і виберіть пункт <strong>«На початковий екран»</strong> (Add to Home Screen).</li>
                                <li>Натисніть <strong>«Додати»</strong> у правому верхньому кутку.</li>
                            </ol>
                        </div>
                    </div>
                </div>

                <!-- Opera & Firefox на Android -->
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed fw-bold text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#androidOtherGuide">
                            🌐 Opera, Firefox, Edge (Android / PC)
                        </button>
                    </h2>
                    <div id="androidOtherGuide" class="accordion-collapse collapse" data-bs-parent="#installGuideAccordion">
                        <div class="accordion-body">
                            <ol class="mb-0">
                                <li class="mb-2">Натисніть меню браузера (три крапки <strong>⋮</strong> або три лінії у кутку екрана).</li>
                                <li class="mb-2">Знайдіть пункт <strong>«Додати на головний екран»</strong> або <strong>«Встановити додаток»</strong>.</li>
                                <li>Підтвердьте додавання на робочий стіл.</li>
                            </ol>
                        </div>
                    </div>
                </div>

                <!-- Google Chrome (якщо кнопка не спрацювала) -->
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed fw-bold text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#chromeGuide">
                            🤖 Google Chrome (Android)
                        </button>
                    </h2>
                    <div id="chromeGuide" class="accordion-collapse collapse" data-bs-parent="#installGuideAccordion">
                        <div class="accordion-body">
                            <p class="mb-2">Зазвичай на головній сторінці з'являється зелена кнопка <strong>«📲 Встановити на телефон»</strong>.</p>
                            <p class="mb-0">Якщо вона зникла, натисніть три крапки <strong>⋮</strong> у верхньому правому кутку Chrome та виберіть <strong>«Встановити додаток»</strong>.</p>
                        </div>
                    </div>
                </div>

            </div>

            <div class="mt-4 text-center">
                <a href="<?= BASE_URL ?>/index.php" class="btn btn-success">
                    Зрозуміло, повернутися до роботи
                </a>
            </div>

        </div>
    </div>
</main>

<?php
require_once __DIR__ . '/../includes/footer.php';
?>
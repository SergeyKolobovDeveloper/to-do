<!DOCTYPE html>
<html lang="ua">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <title><?= $title ?? 'Менеджер задач' ?></title>
</head>
<body class="d-flex flex-column min-vh-100">
    <header>
        <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
            <div class="container">
                <a href="/to-do/index.php" class="navbar-brand fw-bold text-primary d-flex align-items-center">
                    <i class="bi bi-clipboard2-check me-2"></i>
                    <span>TaskFlow</span>
                </a>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <div class="navbar-nav ms-3">
                        <a class="nav-link" href="/to-do/index.php">Головна</a>
                        <a class="nav-link" href="#">Про нас</a>
                        <a class="nav-link" href="#">Контакти</a>
                    </div>
                </div>
                <div class="ms-auto d-flex gap-2">
                    <a href="/to-do/auth/login.php" class="btn btn-outline-success">Увійти</a>
                    <a href="/to-do/auth/register.php" class="btn btn-success">Створити акаунт</a>
                </div>
            </div>
        </nav>
    </header>
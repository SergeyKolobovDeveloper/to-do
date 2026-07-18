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
    <?php $isDashboard = strpos($_SERVER['SCRIPT_NAME'], 'dashboard.php') !== false; ?>
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
                        <?php if(!$isDashboard):?>
                            <a class="nav-link" href="#">Про нас</a>
                            <a class="nav-link" href="#">Контакти</a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="ms-auto d-flex gap-2 align-items-center">
                    <?php if(isset($_SESSION['user'])):?>
                        <span class="me-2 text-muted">
                            <i class="bi bi-person-circle me-1"></i>Привіт, <strong><?= htmlspecialchars($_SESSION['user']['name']) ?></strong>!
                        </span>
                        <?php if(!$isDashboard): ?>
                        <a href="/to-do/pages/dashboard.php" class="btn btn-sm btn-outline-primary fw-semibold">
                            <i class="bi bi-columns-gap me-1"></i>Особистий кабінет
                        </a>
                        <?php endif;?>
                        <a href="/to-do/actions/logout.php" class="btn btn-outline-danger">Вийти</a>
                    <?php else: ?>
                        <a href="/to-do/auth/login.php" class="btn btn-outline-success">Увійти</a>
                        <a href="/to-do/auth/register.php" class="btn btn-success">Створити акаунт</a>
                    <?php endif;?>
                </div>
            </div>
        </nav>
    </header>
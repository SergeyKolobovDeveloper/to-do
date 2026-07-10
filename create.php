<?php
require_once 'db.php';

if($_SERVER['REQUEST_METHOD'] ==='POST' && !empty($_POST['title'])){

    $title = trim($_POST['title']);
    $sql = 'INSERT INTO `tasks` (title) VALUES (:title)';

    $result = $pdo->prepare($sql);
    $result->bindParam(':title', $title);

    $result->execute();

    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <title>Create</title>
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <h1 class="mb-4 text-center">Додати нову задачу!</h1>
                <form action="" method="POST" class="shadow p-4 rounded bg-light">
                    <div class="mb-3">
                        <label for="title" class="form-label">Назва задачі</label>
                        <input type="text" name="title" id="title" class="form-control" placeholder="Введіть текст задачі..." required>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-success">Додати</button>
                        <a href="index.php" class="btn btn-secondary">Назад на головну</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

</body>
</html>
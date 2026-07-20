<?php
session_start();
if(!isset($_SESSION['user'])){
    header('Location: /to-do/auth/login.php');
    exit;
}
require_once "../config/db.php";

if(empty($_GET['id'])){
    header('Location: dashboard.php');
    exit;
}

$id = $_GET['id'];
$userId = $_SESSION['user']['id'];

$sql = 'SELECT * FROM `tasks` WHERE id = :id AND user_id = :user_id';
$result = $pdo->prepare($sql);
$result->execute([
    'id' => $id,
    'user_id' => $userId
    ]);

$task = $result->fetch(PDO::FETCH_ASSOC);
if(!$task){
    header('Location: dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <title>Update</title>
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <h1 class="mb-4 text-center">Редагувати задачу!</h1>
                <form action="../actions/edit-task.php" method="post" class="shadow p-4 rounded bg-light">
                    <div class="mb-3">
                        <label for="title" class="form-label">Назва</label>
                        <input type="text" name="title" id="title" class="form-control" value="<?= htmlspecialchars($task['title']) ?>" required>
                    </div>
                    <input type="hidden" name="id" value="<?= $task['id'] ?>">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Зберегти зміни</button>
                        <a href="dashboard.php" class="btn btn-secondary">Назад на головну</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
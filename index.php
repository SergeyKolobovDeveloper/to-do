<?php
require_once 'db.php';

$sql = "SELECT * FROM `tasks`";

$result = $pdo->prepare($sql);
$result->execute();

$data = $result->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <title>To-Do list</title>
</head>
<body>
    <div class="container mt-5">
        <div class="text-center">
            <h1>Менеджер задач!</h1>
        </div>
        <div>
            <a href="create.php" class="btn btn-success mb-3">Додати задачу!</a>
        </div>
        <br>
        <div class="text-center">
            <table class="table table-bordered border-primary">
                <tr>
                    <th>Назва</th>
                    <th>Дії</th>
                    <th>Статус</th>
                </tr>
                <?php foreach($data as $item):?>
                    <tr>
                        <td><a href="status.php?id=<?=$item['id']?>"><?=htmlspecialchars($item['title'])?></a></td>
                        <td>
                            <div>
                                <a href="update.php?id=<?= $item['id'] ?>"  class="btn btn-primary btn-sm">Редагувати</a>
                                <a href="delete.php?id=<?= $item['id']?>"  class="btn btn-danger btn-sm">Видалити</a>
                            </div>
                        </td>
                        <td>
                            <?php if($item['is_completed'] === 1):?>
                                <span class="badge bg-success">Виконано!</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Не виконано!</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>
</body>
</html>
<?php
session_start();
if (!isset($_SESSION['is_admin'])) {
    header('Location: /VanyaLaba6/login.php');
    exit;
}

require_once '../db.php';

$currentTable = $_GET['table'] ?? 'users';
$editId = $_GET['edit'] ?? null;
$deleteId = $_GET['delete'] ?? null;
$filter = $_GET['filter'] ?? [];

$tables = [
    'users' => 'Пользователи',
    'products' => 'Продукты',
    'subscriptions' => 'Подписки',
];

$tableFields = [
    'products' => [
        'id' => 'ID',
        'title' => 'Название',
        'genre' => 'Жанр',
        'year' => 'Год',
        'category' => 'Категория',
        'image' => 'Изображение'
    ],
    'subscriptions' => [
        'id' => 'ID',
        'name' => 'Название',
        'price' => 'Цена',
        'features' => 'Возможности',
        'recommended' => 'Рекомендуемая',
        'active' => 'Активная'
    ],
    'users' => [
        'id' => 'ID',
        'login' => 'Логин',
        'name' => 'Имя',
        'phone' => 'Телефон',
        'email' => 'Email',
        'registration_date' => 'Дата регистрации',
        'subscription_id' => 'ID подписки',
        'subscription_expires' => 'Окончание подписки'
    ]
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['save_record'])) {
        $id = (int)$_POST['id'];
        $updates = [];
        foreach ($_POST as $field => $value) {
            if ($field !== 'id' && $field !== 'save_record' && array_key_exists($field, $tableFields[$currentTable])) {
                $escapedValue = $conn->real_escape_string($value);
                $updates[] = "$field = '$escapedValue'";
            }
        }
        if (!empty($updates)) {
            $conn->query("UPDATE $currentTable SET " . implode(', ', $updates) . " WHERE id = $id");
        }
    }

    if (isset($_POST['add_record'])) {
        $fields = [];
        $values = [];
        foreach ($_POST as $field => $value) {
            if ($field !== 'add_record' && array_key_exists($field, $tableFields[$currentTable]) && $field !== 'id') {
                $escapedField = $conn->real_escape_string($field);
                $escapedValue = $conn->real_escape_string($value);
                $fields[] = "`$escapedField`";
                $values[] = "'$escapedValue'";
            }
        }
        if (!empty($fields)) {
            $conn->query("INSERT INTO $currentTable (" . implode(', ', $fields) . ") VALUES (" . implode(', ', $values) . ")");
        }
    }

    if (isset($_POST['mass_update']) && $currentTable === 'subscriptions') {
        $action = $_POST['mass_action'];
        $amount = (float)$_POST['mass_amount'];
        if ($action === 'increase') {
            $conn->query("UPDATE subscriptions SET price = price + $amount");
        } elseif ($action === 'decrease') {
            $conn->query("UPDATE subscriptions SET price = GREATEST(price - $amount, 0)");
        } elseif ($action === 'set') {
            $conn->query("UPDATE subscriptions SET price = $amount");
        }
    }

    header("Location: dashboard.php?table=$currentTable");
    exit;
}

if ($deleteId) {
    $conn->query("DELETE FROM $currentTable WHERE id = " . (int)$deleteId);
    header("Location: dashboard.php?table=$currentTable");
    exit;
}

// Формирование SQL запроса с фильтрами
$query = "SELECT * FROM $currentTable";
$where = [];
foreach ($filter as $field => $value) {
    if (!empty($value)) {
        $escapedValue = $conn->real_escape_string($value);
        $where[] = "$field LIKE '%$escapedValue%'";
    }
}
if (!empty($where)) {
    $query .= " WHERE " . implode(' AND ', $where);
}
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Админ панель</title>
    <style>
        :root {
            --primary-color: #3498db;
            --secondary-color: #2980b9;
            --danger-color: #e74c3c;
            --success-color: #2ecc71;
            --warning-color: #f39c12;
            --light-color: #ecf0f1;
            --dark-color: #2c3e50;
            --border-color: #bdc3c7;
            --text-color: #333;
            --text-light: #7f8c8d;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
            color: var(--text-color);
        }

        .header {
            background-color: var(--dark-color);
            color: white;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .header h1 {
            margin: 0;
            font-size: 1.5rem;
        }

        .container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 0 20px;
        }

        .nav-tabs {
            display: flex;
            border-bottom: 1px solid var(--border-color);
            margin-bottom: 20px;
        }

        .nav-tabs a {
            padding: 10px 15px;
            text-decoration: none;
            color: var(--text-color);
            border: 1px solid transparent;
            border-bottom: none;
            border-radius: 4px 4px 0 0;
            margin-right: 5px;
            transition: all 0.3s;
        }

        .nav-tabs a:hover {
            background-color: var(--light-color);
        }

        .nav-tabs a.active {
            background-color: white;
            border-color: var(--border-color);
            border-bottom-color: white;
            color: var(--primary-color);
            font-weight: bold;
        }

        .card {
            background-color: white;
            border-radius: 4px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            overflow: hidden;
        }

        .card-header {
            padding: 15px 20px;
            background-color: var(--light-color);
            border-bottom: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card-body {
            padding: 20px;
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
        }

        th {
            background-color: var(--light-color);
            font-weight: 600;
        }

        tr:hover {
            background-color: rgba(0,0,0,0.02);
        }

        .btn {
            padding: 8px 12px;
            border-radius: 4px;
            border: none;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
        }

        .btn-primary {
            background-color: var(--primary-color);
            color: white;
        }

        .btn-primary:hover {
            background-color: var(--secondary-color);
        }

        .btn-danger {
            background-color: var(--danger-color);
            color: white;
        }

        .btn-danger:hover {
            background-color: #c0392b;
        }

        .btn-success {
            background-color: var(--success-color);
            color: white;
        }

        .btn-success:hover {
            background-color: #27ae60;
        }

        .btn-warning {
            background-color: var(--warning-color);
            color: white;
        }

        .btn-warning:hover {
            background-color: #e67e22;
        }

        .form-control {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid var(--border-color);
            border-radius: 4px;
            box-sizing: border-box;
            margin-bottom: 10px;
            font-size: 14px;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 2px rgba(52,152,219,0.2);
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
        }

        .filter-form {
            background-color: white;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .filter-row {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 15px;
        }

        .filter-field {
            flex: 1;
            min-width: 200px;
        }

        .actions {
            white-space: nowrap;
        }

        .edit-form, .add-form {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 4px;
            margin-bottom: 20px;
            border-left: 4px solid var(--primary-color);
        }

        .mass-update-form {
            background-color: #fff8e1;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
            border-left: 4px solid var(--warning-color);
        }

        .radio-group {
            display: flex;
            gap: 15px;
            margin-bottom: 10px;
        }

        .radio-option {
            display: flex;
            align-items: center;
            gap: 5px;
        }
    </style>
</head>
<body>
<div class="header">
    <h1>Административная панель</h1>
</div>

<div class="container">
    <div class="nav-tabs">
        <?php foreach ($tables as $table => $name): ?>
            <a href="?table=<?= $table ?>" class="<?= $currentTable === $table ? 'active' : '' ?>"><?= $name ?></a>
        <?php endforeach; ?>
    </div>

    <div class="card">
        <div class="card-header">
            <h2>Фильтры</h2>
        </div>
        <div class="card-body">
            <form method="get" class="filter-form">
                <input type="hidden" name="table" value="<?= $currentTable ?>">
                <div class="filter-row">
                    <?php foreach ($tableFields[$currentTable] as $field => $label): ?>
                        <div class="filter-field">
                            <label for="filter_<?= $field ?>"><?= $label ?></label>
                            <input type="text" id="filter_<?= $field ?>" name="filter[<?= $field ?>]"
                                   value="<?= htmlspecialchars($filter[$field] ?? '') ?>"
                                   class="form-control" placeholder="Фильтр по <?= $label ?>">
                        </div>
                    <?php endforeach; ?>
                </div>
                <button type="submit" class="btn btn-primary">Применить фильтры</button>
                <a href="?table=<?= $currentTable ?>" class="btn btn-danger">Сбросить</a>
            </form>
        </div>
    </div>

    <?php if ($currentTable === 'subscriptions'): ?>
        <div class="mass-update-form">
            <h3>Массовое редактирование цен на подписки</h3>
            <form method="post">
                <div class="radio-group">
                    <label class="radio-option"><input type="radio" name="mass_action" value="increase" checked> Увеличить на</label>
                    <label class="radio-option"><input type="radio" name="mass_action" value="decrease"> Уменьшить на</label>
                    <label class="radio-option"><input type="radio" name="mass_action" value="set"> Установить цену</label>
                </div>
                <div style="display: flex; gap: 10px; align-items: center;">
                    <input type="number" name="mass_amount" class="form-control" style="width: 150px;" required>
                    <button type="submit" name="mass_update" class="btn btn-warning">Применить ко всем</button>
                </div>
            </form>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-header">
            <h2>Данные таблицы <?= $tables[$currentTable] ?></h2>
        </div>
        <div class="card-body">
            <table>
                <thead>
                <tr>
                    <?php foreach ($tableFields[$currentTable] as $field => $label): ?>
                        <th><?= $label ?></th>
                    <?php endforeach; ?>
                    <th class="actions">Действия</th>
                </tr>
                </thead>
                <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <?php if ($editId == $row['id']): ?>
                            <form method="post">
                                <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                <?php foreach ($tableFields[$currentTable] as $field => $label): ?>
                                    <td>
                                        <?php if ($field === 'id'): ?>
                                            <?= $row[$field] ?>
                                        <?php else: ?>
                                            <input type="text" name="<?= $field ?>" value="<?= htmlspecialchars($row[$field]) ?>" class="form-control">
                                        <?php endif; ?>
                                    </td>
                                <?php endforeach; ?>
                                <td>
                                    <button type="submit" name="save_record" class="btn btn-success">Сохранить</button>
                                    <a href="?table=<?= $currentTable ?>" class="btn btn-danger">Отмена</a>
                                </td>
                            </form>
                        <?php else: ?>
                            <?php foreach ($tableFields[$currentTable] as $field => $label): ?>
                                <td><?= htmlspecialchars($row[$field]) ?></td>
                            <?php endforeach; ?>
                            <td class="actions">
                                <a href="?table=<?= $currentTable ?>&edit=<?= $row['id'] ?>" class="btn btn-primary">Редактировать</a>
                                <a href="?table=<?= $currentTable ?>&delete=<?= $row['id'] ?>" onclick="return confirm('Вы уверены, что хотите удалить эту запись?')" class="btn btn-danger">Удалить</a>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h2>Добавить новую запись</h2>
        </div>
        <div class="card-body">
            <form method="post" class="add-form">
                <div class="form-row" style="display: flex; flex-wrap: wrap; gap: 15px;">
                    <?php foreach ($tableFields[$currentTable] as $field => $label): ?>
                        <?php if ($field === 'id') continue; ?>
                        <div class="form-group" style="flex: 1; min-width: 200px;">
                            <label for="add_<?= $field ?>"><?= $label ?></label>
                            <input type="text" id="add_<?= $field ?>" name="<?= $field ?>" class="form-control">
                        </div>
                    <?php endforeach; ?>
                </div>
                <button type="submit" name="add_record" class="btn btn-success">Добавить запись</button>
            </form>
        </div>
    </div>
</div>
</body>
</html>
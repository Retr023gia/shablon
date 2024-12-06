<?php
include 'db.php';

// Инициализация переменной фильтра
$partner_address_filter = '';

// Получаем значение фильтра из запроса
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $partner_address_filter = $_GET['partner_address_filter'] ?? '';
}

// Формируем SQL запрос с учетом фильтра
$sql = "SELECT partners.id, partners.name, partners.address, 
               SUM(sales.amount * products.price) AS total_revenue
        FROM partners
        LEFT JOIN sales ON partners.id = sales.partner_id
        LEFT JOIN products ON sales.product_id = products.id
        WHERE 1=1";

if ($partner_address_filter) {
    $sql .= " AND partners.address LIKE '%" . $conn->real_escape_string($partner_address_filter) . "%'";
}

$sql .= " GROUP BY partners.id";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Просмотр партнеров</title>
</head>
<body>

<h1>Просмотр партнеров</h1>
<a href="index.php">Просмотреть продажи</a>

<!-- Форма фильтрации -->
<form method="GET">
    <input type="text" name="partner_address_filter" placeholder="Поиск по адресу партнеров" value="<?php echo htmlspecialchars($partner_address_filter); ?>">
    <button type="submit">Фильтровать</button>
</form>

<table>
    <thead>
        <tr>
            <th>Имя партнера</th>
            <th>Адрес партнера</th>
            <th>Суммарная выручка</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['name']; ?></td>
                    <td><?php echo $row['address']; ?></td>
                    <td><?php echo $row['total_revenue'] ? number_format($row['total_revenue'], 2, ',', ' ') : '0.00'; ?> руб.</td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="3">Нет записей о партнерах.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

</body>
</html>

<?php
$conn->close();
?>

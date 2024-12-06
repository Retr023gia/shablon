<?php
include 'db.php';

// Инициализация переменных фильтрации
$partner_filter = '';
$date_filter = '';
$min_price = '';
$max_price = '';

// Получаем значения фильтров из запроса
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    // Получаем ID партнера из выпадающего списка
    $partner_filter = $_GET['partner_filter'] ?? '';
    $date_filter = $_GET['date_filter'] ?? '';
    $min_price = $_GET['min_price'] ?? '';
    $max_price = $_GET['max_price'] ?? '';
}

// Получаем список партнеров
$partners_query = "SELECT * FROM partners";
$partners_result = $conn->query($partners_query);

// Формируем SQL запрос с учетом фильтров
$sql = "SELECT sales.date, partners.name AS partner_name, products.name AS product_name, 
               sales.amount AS quantity, products.price 
        FROM sales 
        JOIN partners ON sales.partner_id = partners.id 
        JOIN products ON sales.product_id = products.id 
        WHERE 1=1";

if ($partner_filter) {
    $sql .= " AND sales.partner_id = " . (int)$partner_filter;
}

if ($date_filter) {
    $sql .= " AND sales.date = '" . $conn->real_escape_string($date_filter) . "'";
}

if ($min_price != '' && $max_price != '') {
    $sql .= " AND products.price BETWEEN " . $conn->real_escape_string($min_price) . " AND " . $conn->real_escape_string($max_price);
} elseif ($min_price != '') {
    $sql .= " AND products.price >= " . $conn->real_escape_string($min_price);
} elseif ($max_price != '') {
    $sql .= " AND products.price <= " . $conn->real_escape_string($max_price);
}

$sql .= " ORDER BY sales.date DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Просмотр продаж</title>
    <script>
        function validateDate() {
            const dateInput = document.querySelector('input[name="date_filter"]');
            const selectedDate = new Date(dateInput.value);
            const today = new Date();
            today.setHours(0, 0, 0, 0); // Убираем время

            if (selectedDate > today) {
                alert('Выбранная дата не может быть в будущем.');
                return false; // Отклоняем отправку формы
            }
            return true; // Разрешаем отправку формы
        }
    </script>
</head>
<body>

<h1>Просмотр продаж</h1>
<a href="view_partners.php">Просмотреть партнеров</a>
<a href="add_sale.php">Добавить продажу</a>

<!-- Форма фильтрации -->
<form method="GET" onsubmit="return validateDate();">
    <!-- Выпадающий список для выбора партнера -->
    <select name="partner_filter">
        <option value="">Все партнеры</option>
        <?php while($partner = $partners_result->fetch_assoc()): ?>
            <option value="<?php echo $partner['id']; ?>" <?php echo $partner['id'] == $partner_filter ? 'selected' : ''; ?>>
                <?php echo $partner['name']; ?>
            </option>
        <?php endwhile; ?>
    </select>
    
    <input type="date" name="date_filter" placeholder="Дата продажи" value="<?php echo htmlspecialchars($date_filter); ?>">
    <input type="number" name="min_price" placeholder="Мин. цена" value="<?php echo htmlspecialchars($min_price); ?>">
    <input type="number" name="max_price" placeholder="Макс. цена" value="<?php echo htmlspecialchars($max_price); ?>">
    <button type="submit">Фильтровать</button>
</form>

<table>
    <thead>
        <tr>
            <th>Дата продажи</th>
            <th>Партнер</th>
            <th>Товар</th>
            <th>Количество</th>
            <th>Цена</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['date']; ?></td>
                    <td><?php echo $row['partner_name']; ?></td>
                    <td><?php echo $row['product_name']; ?></td>
                    <td><?php echo $row['quantity']; ?></td>
                    <td><?php echo $row['price']; ?> руб.</td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="5">Нет записей о продажах.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

</body>
</html>

<?php
$conn->close();
?>
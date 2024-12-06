<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $partner_id = $_POST['partner_id'];
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];
    $sale_date = $_POST['sale_date'];

    // Проверка, что дата продажи не в будущем
    if ($sale_date > date('Y-m-d')) {
        echo "<script>alert('Дата продажи не может быть в будущем.');</script>";
    } else {
        // SQL-запрос для вставки данных о продаже
        $sql = "INSERT INTO sales (partner_id, product_id, amount, date) VALUES ($partner_id, $product_id, $quantity, '$sale_date')";
        if ($conn->query($sql) === TRUE) {
            echo "<script>alert('Запись о продаже добавлена.'); window.location='index.php';</script>";
        } else {
            echo "Ошибка: " . $conn->error;
        }
    }
}

// Получение всех партнеров и продуктов из базы данных
$partners = $conn->query("SELECT * FROM partners");
$products = $conn->query("SELECT * FROM products");
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Добавление продажи</title>
</head>
<body>

<h1>Добавление продажи</h1>
<a href="index.php">Вернуться к продажам</a>

<form method="POST">
    <label for="partner_id">Партнер:</label>
    <select id="partner_id" name="partner_id" required>
        <?php while($row = $partners->fetch_assoc()): ?>
            <option value="<?php echo $row['id']; ?>"><?php echo $row['name']; ?></option>
        <?php endwhile; ?>
    </select>
    
    <label for="product_id">Товар:</label>
    <select id="product_id" name="product_id" required>
        <?php while($row = $products->fetch_assoc()): ?>
            <option value="<?php echo $row['id']; ?>"><?php echo $row['name']; ?></option>
        <?php endwhile; ?>
    </select>

    <label for="quantity">Количество:</label>
    <input type="number" id="quantity" name="quantity" required min="1">

    <label for="sale_date">Дата:</label>
    <input type="date" id="sale_date" name="sale_date" required>

    <button type="submit">Добавить</button>
</form>

</body>
</html>

<?php
$conn->close();
?>

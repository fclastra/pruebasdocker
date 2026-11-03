<?php
$host    = 'db'; 
$db      = 'mi_base_de_datos';
$user    = 'usuario_web';
$pass    = 'tu_password';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset;port=3306";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);

    $sqlCreateTable = "CREATE TABLE IF NOT EXISTS usuario (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nombre VARCHAR(100) NOT NULL
    ) ENGINE=InnoDB;";
    $pdo->exec($sqlCreateTable);

    $mensaje = "";
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['nombre'])) {
        $nuevoNombre = trim($_POST['nombre']);
        
        $sqlInsert = "INSERT INTO usuario (nombre) VALUES (:nombre)";
        $stmt = $pdo->prepare($sqlInsert);
        $stmt->execute(['nombre' => $nuevoNombre]);
        
        header("Location: " . $_SERVER['PHP_SELF'] . "?guardado=1");
        exit;
    }

    if (isset($_GET['guardado'])) {
        $mensaje = "<p style='color: green; font-weight: bold;'>¡Usuario guardado con éxito!</p>";
    }

    $stmt = $pdo->query("SELECT id, nombre FROM usuario ORDER BY id DESC");
    $usuarios = $stmt->fetchAll();

} catch (\PDOException $e) {
    die("<p style='color: red;'>Error en la base de datos: " . $e->getMessage() . "</p>");
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Prueba de Clase - PHP & MySQL en Docker</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 30px; background-color: #f4f4f4; }
        .container { background: white; padding: 20px; border-radius: 8px; max-width: 500px; box-shadow: 0px 0px 10px rgba(0,0,0,0.1); }
        input[type="text"] { width: 80%; padding: 8px; margin-bottom: 10px; }
        input[type="submit"] { padding: 8px 15px; background-color: #007BFF; color: white; border: none; cursor: pointer; }
        input[type="submit"]:hover { background-color: #0056b3; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
<?php
try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    echo "<p style='color: green; font-weight: bold;'>✔ ¡Conexión exitosa a la base de datos MySQL dentro de Docker!</p>";
    
    $stmt = $pdo->query("SELECT VERSION() as version");
    $row = $stmt->fetch();
    echo "<p>Versión del servidor MySQL: <strong>" . $row['version'] . "</strong></p>";

} catch (\PDOException $e) {
    echo "<p style='color: red; font-weight: bold;'>✘ Error de conexión: " . $e->getMessage() . "</p>";
}
?>

<div class="container">
    <h2>Registro de Usuarios</h2>
    
    <?= $mensaje; ?>

    <form method="POST" action="">
        <label for="nombre">Nombre del alumno / usuario:</label><br><br>
        <input type="text" id="nombre" name="nombre" placeholder="Ej. Garo" required>
        <input type="submit" value="Guardar en BD">
    </form>

    <hr style="margin-top: 30px; border: 0; border-top: 1px solid #ccc;">

    <h3>Usuarios en la Base de Datos</h3>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($usuarios) > 0): ?>
                <?php foreach ($usuarios as $u): ?>
                    <tr>
                        <td><?= htmlspecialchars($u['id']) ?></td>
                        <td><?= htmlspecialchars($u['nombre']) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="2" style="text-align: center; color: #777;">No hay usuarios registrados aún.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>
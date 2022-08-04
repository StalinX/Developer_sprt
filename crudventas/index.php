<?php
include 'funciones.php';

csrf();
if (isset($_POST['submit']) && !hash_equals($_SESSION['csrf'], $_POST['csrf'])) {
  die();
}

$error = false;
$config = include 'config.php';
$clientes = '';

try {
  $dsn = 'mysql:host=' . $config['db']['host'] . ';dbname=' . $config['db']['name'];
  $conexion = new PDO($dsn, $config['db']['user'], $config['db']['pass'], $config['db']['options']);

  // List records
  if (isset($_POST['apellido'])) {
    $consultaSQL = "SELECT * FROM clientes WHERE apellido LIKE '%" . $_POST['apellido'] . "%'" . " ORDER BY apellido";
  } else {
    $consultaSQL = "SELECT * FROM clientes";
  }

  //echo $consultaSQL; die();

  $sentencia = $conexion->prepare($consultaSQL);
  $sentencia->execute();

  $clientes = $sentencia->fetchAll();

} catch(PDOException $error) {
  $error= $error->getMessage();
}

$titulo = isset($_POST['apellido']) ? 'Lista de clientes (' . $_POST['apellido'] . ')' : 'Lista de clientes';
?>

<?php include "templates/header.php"; ?>

<?php
if ($error) {
  ?>
  <div class="container mt-2">
    <div class="row">
      <div class="col-md-12">
        <div class="alert alert-danger" role="alert">
          <?= $error ?>
        </div>
      </div>
    </div>
  </div>
  <?php
}
?>

<div class="container">
  <div class="row">
    <div class="col-md-12">
      <a href="crear.php"  class="btn btn-primary mt-4">Nuevo cliente</a>
      <hr>
      <form method="post" class="form-inline">
        <div class="form-group mr-3">
          <input type="text" id="apellido" name="apellido" placeholder="Buscar por apellido" class="form-control">
        </div>
        <input name="csrf" type="hidden" value="<?php echo escapar($_SESSION['csrf']); ?>">
        <button type="submit" name="submit" class="btn btn-primary">Buscar</button>
      </form>
    </div>
  </div>
</div>

<div class="container">
  <div class="row">
    <div class="col-md-12">
      <h2 class="mt-3"><?= $titulo ?></h2>
      <table class="table">
        <thead>
          <tr>
            <th>#</th>
            <th>Nombre</th>
            <th>Apellido</th>
            <th>Email</th>
            <th>Telefono</th>
            <th>Direccion</th>
            <th>Nacionalidad</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php
          if ($clientes && $sentencia->rowCount() > 0) {
            foreach ($clientes as $fila) {
              ?>
              <tr>
                <td><?php echo escapar($fila["id"]); ?></td>
                <td><?php echo escapar($fila["nombre"]); ?></td>
                <td><?php echo escapar($fila["apellido"]); ?></td>
                <td><?php echo escapar($fila["email"]); ?></td>
                <td><?php echo escapar($fila["telefono"]); ?></td>
                <td><?php echo escapar($fila["direccion"]); ?></td>
                <td><?php echo escapar($fila["nacionalidad"]); ?></td>
                <td>
                  <a href="<?= 'borrar.php?id=' . escapar($fila["id"]) ?>">🗑️Eliminar</a>
                  <a href="<?= 'editar.php?id=' . escapar($fila["id"]) ?>">✏️Editar</a>
                </td>
              </tr>
              <?php
            }
          }
          ?>
        <tbody>
      </table>
    </div>
  </div>
</div>

<?php include "templates/footer.php"; ?>
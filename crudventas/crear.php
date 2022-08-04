<?php

include 'funciones.php';

csrf();
if (isset($_POST['submit']) && !hash_equals($_SESSION['csrf'], $_POST['csrf'])) {
  die();
}

if (isset($_POST['submit'])) {
  $resultado = [
    'error' => false,
    'mensaje' => 'El cliente ' . escapar($_POST['nombre']) . ' ha sido agregado con éxito'
  ];

  $config = include 'config.php';

  try {
    $dsn = 'mysql:host=' . $config['db']['host'] . ';dbname=' . $config['db']['name'];
    $conexion = new PDO($dsn, $config['db']['user'], $config['db']['pass'], $config['db']['options']);

    $clientes = [
      "nombre"   => $_POST['nombre'],
      "apellido" => $_POST['apellido'],
      "email"    => $_POST['email'],
      "telefono" => $_POST['telefono'],
      "direccion"=> $_POST['direccion'],
      "nacionalidad" => $_POST['nacionalidad'],
    ];

    // Insert record
    $consultaSQL = "INSERT INTO clientes (nombre, apellido, email, telefono, direccion, nacionalidad)";
    $consultaSQL .= "values (:" . implode(", :", array_keys($clientes)) . ")";

    //echo $consultaSQL; die();


    $sentencia = $conexion->prepare($consultaSQL);
    $sentencia->execute($clientes);

  } catch(PDOException $error) {
    $resultado['error'] = true;
    $resultado['mensaje'] = $error->getMessage();
  }
}
?>

<?php include 'templates/header.php'; ?>

<?php
if (isset($resultado)) {
  ?>
  <div class="container mt-3">
    <div class="row">
      <div class="col-md-12">
        <div class="alert alert-<?= $resultado['error'] ? 'danger' : 'success' ?>" role="alert">
          <?= $resultado['mensaje'] ?>
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
      <h2 class="mt-4">Agregar nuevo cliente</h2>
      <hr>
      <form method="post">
        <div class="form-group">
          <label for="nombre">Nombre</label>
          <input type="text" name="nombre" id="nombre" class="form-control">
        </div>
        <div class="form-group">
          <label for="apellido">Apellido</label>
          <input type="text" name="apellido" id="apellido" class="form-control">
        </div>
        <div class="form-group">
          <label for="email">Email</label>
          <input type="email" name="email" id="email" class="form-control">
        </div>
        <div class="form-group">
          <label for="telefono">Telefono</label>
          <input type="text" name="telefono" id="telefono" class="form-control">
        </div>
        <div class="form-group">
          <label for="direccion">Direccion</label>
          <input type="text" name="direccion" id="direccion" class="form-control">
        </div>
        <div class="form-group">
          <label for="nacionalidad">Nacionalidad</label>
          <input type="text" name="nacionalidad" id="nacionalidad" class="form-control">
        </div>
        <div class="form-group">
          <input name="csrf" type="hidden" value="<?php echo escapar($_SESSION['csrf']); ?>">
          <input type="submit" name="submit" class="btn btn-primary" value="Guardar">
          <a class="btn btn-secondary" href="index.php">Lista de clientes</a>
        </div>
      </form>
    </div>
  </div>
</div>

<?php include 'templates/footer.php'; ?>
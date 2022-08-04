<?php
include 'funciones.php';

csrf();
if (isset($_POST['submit']) && !hash_equals($_SESSION['csrf'], $_POST['csrf'])) {
  die();
}

$config = include 'config.php';

$resultado = [
  'error' => false,
  'mensaje' => ''
];

if (!isset($_GET['id'])) {
  $resultado['error'] = true;
  $resultado['mensaje'] = 'El cliente no existe';
}

if (isset($_POST['submit'])) {
  try {
    $dsn = 'mysql:host=' . $config['db']['host'] . ';dbname=' . $config['db']['name'];
    $conexion = new PDO($dsn, $config['db']['user'], $config['db']['pass'], $config['db']['options']);

    $clientes = [
      "id"        => $_GET['id'],
      "nombre"   => $_POST['nombre'],
      "apellido" => $_POST['apellido'],
      "email"    => $_POST['email'],
      "telefono" => $_POST['telefono'],
      "direccion"=> $_POST['direccion'],
      "nacionalidad" => $_POST['nacionalidad']
    ];
    
    // Update record
    $consultaSQL = "UPDATE clientes SET
        nombre = :nombre,
        apellido = :apellido,
        email = :email,
        telefono = :telefono,
        direccion = :direccion,
        nacionalidad = :nacionalidad,
        updated_at = NOW()
        WHERE id = :id";
    //echo $consultaSQL; die();
    $consulta = $conexion->prepare($consultaSQL);
    $consulta->execute($clientes);

  } catch(PDOException $error) {
    $resultado['error'] = true;
    $resultado['mensaje'] = $error->getMessage();
  }
}

try {
  $dsn = 'mysql:host=' . $config['db']['host'] . ';dbname=' . $config['db']['name'];
  $conexion = new PDO($dsn, $config['db']['user'], $config['db']['pass'], $config['db']['options']);
    
  $id = $_GET['id'];
  $consultaSQL = "SELECT * FROM clientes WHERE id =" . $id;

  //echo $consultaSQL; die();

  $sentencia = $conexion->prepare($consultaSQL);
  $sentencia->execute();

  $clientes = $sentencia->fetch(PDO::FETCH_ASSOC);

  if (!$clientes) {
    $resultado['error'] = true;
    $resultado['mensaje'] = 'No se ha encontrado el cliente';
  }

} catch(PDOException $error) {
  $resultado['error'] = true;
  $resultado['mensaje'] = $error->getMessage();
}
?>

<?php require "templates/header.php"; ?>

<?php
if ($resultado['error']) {
  ?>
  <div class="container mt-2">
    <div class="row">
      <div class="col-md-12">
        <div class="alert alert-danger" role="alert">
          <?= $resultado['mensaje'] ?>
        </div>
      </div>
    </div>
  </div>
  <?php
}
?>

<?php
if (isset($_POST['submit']) && !$resultado['error']) {
  ?>
  <div class="container mt-2">
    <div class="row">
      <div class="col-md-12">
        <div class="alert alert-success" role="alert">
          El cliente ha sido actualizado correctamente
        </div>
      </div>
    </div>
  </div>
  <?php
}
?>

<?php
if (isset($clientes) && $clientes) {
  ?>
  <div class="container">
    <div class="row">
      <div class="col-md-12">
        <h2 class="mt-4">Editando el cliente <?= escapar($clientes['nombre']) . ' ' . escapar($clientes['apellido'])  ?></h2>
        <hr>
        <form method="post">
          <div class="form-group">
            <label for="nombre">Nombre</label>
            <input type="text" name="nombre" id="nombre" value="<?= escapar($clientes['nombre']) ?>" class="form-control">
          </div>
          <div class="form-group">
            <label for="apellido">Apellido</label>
            <input type="text" name="apellido" id="apellido" value="<?= escapar($clientes['apellido']) ?>" class="form-control">
          </div>
          <div class="form-group">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" value="<?= escapar($clientes['email']) ?>" class="form-control">
          </div>
          <div class="form-group">
            <label for="telefono">Telefono</label>
            <input type="text" name="telefono" id="telefono" value="<?= escapar($clientes['telefono']) ?>" class="form-control">
          </div>
          <div class="form-group">
            <label for="direccion">Direccion</label>
            <input type="text" name="direccion" id="direccion" value="<?= escapar($clientes['direccion']) ?>" class="form-control">
          </div>
          <div class="form-group">
            <label for="nacionalidad">Nacionalidad</label>
            <input type="text" name="nacionalidad" id="nacionalidad" value="<?= escapar($clientes['nacionalidad']) ?>" class="form-control">
          </div>
          <div class="form-group">
            <input name="csrf" type="hidden" value="<?php echo escapar($_SESSION['csrf']); ?>">
            <input type="submit" name="submit" class="btn btn-primary" value="Actualizar">
            <a class="btn btn-primary" href="index.php">Regresar al inicio</a>
          </div>
        </form>
      </div>
    </div>
  </div>
  <?php
}
?>

<?php require "templates/footer.php"; ?>
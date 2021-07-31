<?php include("../template/cabecera.php");?>

<?php
    include("../config/bd.php");

    $txtID=(isset($_POST['txtID']))?$_POST['txtID']:"";
    $txtNombre=(isset($_POST['txtNombre']))?$_POST['txtNombre']:"";
    $txtImagen=(isset($_FILES['txtImagen']['name']))?$_FILES['txtImagen']['name']:"";
    $accion=(isset($_POST['accion']))?$_POST['accion']:"";



    switch($accion){

        case "Agregar":
            
            /**Sentenica SQL a ejecutar */
            $sentenciaSQL=$conexion->prepare("INSERT INTO libros (nombre, imagen) VALUES (:nombre, :imagen);");
            
            /**Lineas que retoman los datos insertados por el usuario */
            $sentenciaSQL->bindParam(':nombre', $txtNombre);

            $fecha = new DateTime();
            $nombreArchivo=($txtImagen!="")?$fecha->getTimestamp()."_".$_FILES["txtImagen"]["name"]:"imagen.jpg";
            $tmpImagen=$_FILES["txtImagen"]["tmp_name"];

            if($tmpImagen!=""){
                move_uploaded_file($tmpImagen, "../../img/".$nombreArchivo);
            }

            $sentenciaSQL->bindParam(':imagen', $nombreArchivo);
            
            /**Linea que ejecuta la sentencia SQL */
            $sentenciaSQL->execute();
            
            /**Redirección a productos */
            header("Location:productos.php");
            break;

        case "Modificar":

            /**Sentencia SQL a ejecutar */
            $sentenciaSQL= $conexion->prepare("UPDATE libros SET nombre=:nombre WHERE id=:id");
            $sentenciaSQL->bindParam(':id', $txtID);
            $sentenciaSQL->bindParam(':nombre', $txtNombre);
            /**Linea que ejecuta la sentencia SQL */
            $sentenciaSQL-> execute();


            if($txtImagen!=""){

                /**Cambia el nombre de la imagen en conjunto con la fecha*/
                $fecha = new DateTime();
                $nombreArchivo=($txtImagen!="")?$fecha->getTimestamp()."_".$_FILES["txtImagen"]["name"]:"imagen.jpg";
                $tmpImagen=$_FILES["txtImagen"]["tmp_name"];
                move_uploaded_file($tmpImagen, "../../img/".$nombreArchivo);

                
                $sentenciaSQL= $conexion->prepare("SELECT imagen FROM libros WHERE id=:id");
                $sentenciaSQL->bindParam(':id', $txtID);
                $sentenciaSQL->execute();
                $libro=$sentenciaSQL->fetch(PDO::FETCH_LAZY);

                if(isset($libro["imagen"]) && ($libro["imagen"]!="imagen.jpg")){
                    if(file_exists("../../img/".$libro["imagen"])){
                        unlink("../../img/".$libro["imagen"]);
                    }
                }
                
                /**Sentencia SQL a ejecutar */
                $sentenciaSQL= $conexion->prepare("UPDATE libros SET imagen=:imagen WHERE id=:id");
                $sentenciaSQL->bindParam(':id', $txtID);
                $sentenciaSQL->bindParam(':imagen', $nombreArchivo);
                /**Linea que ejecuta la sentencia SQL */
                $sentenciaSQL-> execute();
            }

            header("Location:productos.php");

            break;

        case "Cancelar":
            
            header("Location:productos.php");

            break;

            case "Seleccionar":

                /**Sentencia SQL a ejecutar */
                $sentenciaSQL= $conexion->prepare("SELECT * FROM libros WHERE id=:id");
                $sentenciaSQL->bindParam(':id', $txtID);
                /**Linea que ejecuta la sentencia SQL */
                $sentenciaSQL-> execute();
                /**Recupera y guarda todos los datos recuperados */
                $libro=$sentenciaSQL->fetch(PDO::FETCH_LAZY);
                
                /**Asignan los valores recuperados de la base de datos */
                $txtNombre=$libro['nombre'];
                $txtImagen=$libro['imagen'];


                break;

                case "Borrar":

                    $sentenciaSQL= $conexion->prepare("SELECT imagen FROM libros WHERE id=:id");
                    $sentenciaSQL->bindParam(':id', $txtID);
                    $sentenciaSQL->execute();
                    $libro=$sentenciaSQL->fetch(PDO::FETCH_LAZY);

                    if(isset($libro["imagen"]) && ($libro["imagen"]!="imagen.jpg")){
                        if(file_exists("../../img/".$libro["imagen"])){
                            unlink("../../img/".$libro["imagen"]);
                        }
                    }

                    /**Sentencia SQL a ejecutar */
                   $sentenciaSQL= $conexion->prepare("DELETE FROM libros WHERE id=:id");
                    $sentenciaSQL->bindParam(':id', $txtID);
                    /**Linea que ejecuta la sentencia SQL */
                    $sentenciaSQL-> execute();
                    
                    header("Location:productos.php");

                    break;
        }

                    /**Sentencia SQL a ejecutar */
                    $sentenciaSQL= $conexion->prepare("SELECT * FROM libros");
                    /**Linea que ejecuta la sentencia SQL */
                    $sentenciaSQL-> execute();
                    /**Recupera y guarda todos los datos recuperados */
                    $listaLibros=$sentenciaSQL->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="col-md-5">
  
<div class="card">
    <div class="card-header">
        Datos de libro
    </div>
    <div class="card-body">
    
    <form method="POST" enctype="multipart/form-data">
    <div class = "form-group">
    <label for="txtID">ID</label>
    <input type="text" required readonly class="form-control" value="<?php echo $txtID; ?>" name="txtID" id="txtID" placeholder="ID">
    </div>

    <div class = "form-group">
    <label for="txtNombre">Nombre:</label>
    <input type="text" required class="form-control" value="<?php echo $txtNombre; ?>" name="txtNombre" id="txtNombre" placeholder="Nombre libro">
    </div>

    <div class = "form-group">
    <label for="txtNombre">Imagen:</label>
    
    <br/>

    <?php if($txtImagen!=""){ ?>
        
        <img class="img-thumbnail rounded" src="../../img/<?php echo $txtImagen;?>" width="150" alt="" srcset="">
    
        <?php } ?>

    <input type="file" class="form-control" name="txtImagen" id="txtImagen" placeholder="ID">
    </div>

    <div class="btn-group" role="group" aria-label="">
        <button type="submit" name="accion" <?php echo ($accion=="Seleccionar")?"disabled":"";?> value="Agregar" class="btn btn-success">Agregar</button>
        <button type="submit" name="accion" <?php echo ($accion!="Seleccionar")?"disabled":"";?> value="Modificar" class="btn btn-warning">Modificar</button>
        <button type="submit" name="accion" <?php echo ($accion!="Seleccionar")?"disabled":"";?> value="Cancelar" class="btn btn-info">Cancelar</button>
    </div>

    </form>

    </div>
</div>


</div>

<div class="col-md-7">
    
<table class="table table-bordered">
    <thead>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Imagen</th>
            <th>Acciones</th>
        </tr>
    </thead>

    <tbody>
        <?php foreach($listaLibros as $libro) { ?>
        <tr>
            <td><?php echo $libro['id']; ?></td>
            <td><?php echo $libro['nombre'];?></td>
            <td>
            <img class="img-thumbnail rounded" src="../../img/<?php echo $libro['imagen'];?>" width="150" alt="" srcset="">
            </td>
            <td>
            <form method="POST">
                <input type="hidden" name="txtID" id="txtID" value="<?php echo $libro['id']; ?>">
                <input type="submit" name="accion" value="Seleccionar" class="btn btn-primary"/>
                <input type="submit" name="accion" value="Borrar" class="btn btn-danger"/>
            </form>    
            </td>
        </tr>
        <?php } ?>
    </tbody>
</table>
</div>


<?php include("../template/pie.php");?>
<?php

//mpc
class Criptomoneda
{
    public $id;
    public $precio;
    public $nombre; 
    public $nacionalidad;
    public $foto;
    

    public function crearCriptomoneda()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $gestorArchivos = new GestorDeArchivos("../FotosAltasCriptos");


        $nuevaUrl=  "/" . "AltaCripto_" . $this->nombre .  ".jpg";
        $this->foto = $nuevaUrl;

        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO criptomonedas (precio, nombre, nacionalidad, foto) VALUES (:precio, :nombre, :nacionalidad,:foto)");
        $consulta->bindValue(':precio', $this->precio, PDO::PARAM_STR);
        $consulta->bindValue(':nombre', $this->nombre, PDO::PARAM_STR);
        $consulta->bindValue(':nacionalidad', $this->nacionalidad, PDO::PARAM_STR);
        $consulta->bindValue(':foto', $this->foto,PDO::PARAM_STR);
        $consulta->execute();
        

        $nombreArchivo =  $this->foto;
        $gestorArchivos->GuardarArchivoVenta($nombreArchivo);
        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, precio, nombre, nacionalidad, foto FROM criptomonedas WHERE fechaBaja IS NULL");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Criptomoneda');
    }

    public static function obtenerLogs()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id_usuario, id_cripto, accion, fechaAccion FROM logs");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function obtenerCriptomonedaId($id)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, precio, nombre, nacionalidad, foto FROM criptomonedas WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Criptomoneda');
    }
    
    public static function obtenerTodosPorNacionalidad($nacionalidad)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, precio, nombre, nacionalidad, foto FROM criptomonedas WHERE nacionalidad = :nacionalidad"); // WHERE fechaBaja IS NULL");
        $consulta->bindValue(':nacionalidad', $nacionalidad, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Criptomoneda');
    }


    
    public function modificarCriptomoneda()
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $gestorArchivos = new GestorDeArchivos("../FotosAltasCriptos");

        $nuevaUrl=  "/" . "AltaCripto_" . $this->nombre .  ".jpg";
        $this->foto = $nuevaUrl;

        $consulta = $objAccesoDato->prepararConsulta("UPDATE criptomonedas SET precio = :precio, nombre = :nombre,
        nacionalidad = :nacionalidad, foto = :foto WHERE id = :id");

        $consulta->bindValue(':precio', $this->precio, PDO::PARAM_INT);
        $consulta->bindValue(':nombre', $this->nombre, PDO::PARAM_STR);
        $consulta->bindValue(':nacionalidad', $this->nacionalidad, PDO::PARAM_STR);
        $consulta->bindValue(':foto', $this->foto, PDO::PARAM_STR);
        $consulta->bindValue(':id', $this->id, PDO::PARAM_INT);

        $nombreArchivo =  $this->foto;
        $gestorArchivos->GuardarArchivoVenta($nombreArchivo);
        
        return $consulta->execute();
    }
    

    
    public static function borrarCriptomoneda($id)
    {
        $fechaBaja = new DateTime(date("d-m-Y"));
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE criptomonedas SET fechaBaja = :fechaBaja WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
       
       $consulta->bindValue(':fechaBaja', date_format($fechaBaja, 'Y-m-d'));
        return $consulta->execute();
    }
    public function LogDeBorrado($criptoId)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $usuario = 1;
        $fechaActual = date('Y-m-d');
    
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO logs (id_usuario, id_cripto, accion, fechaAccion) 
            VALUES (:id_usuario, :id_cripto, NULL, :fechaAccion)");
    
        $consulta->bindValue(':id_usuario', $usuario, PDO::PARAM_INT);
        $consulta->bindValue(':id_cripto', $criptoId, PDO::PARAM_INT);
        $consulta->bindValue(':fechaAccion', $fechaActual, PDO::PARAM_STR);
    
        $consulta->execute();
    }
     
    
    public static function verificarId($id)
    {
      $objAccesoDato = AccesoDatos::obtenerInstancia();
      $consulta = $objAccesoDato->prepararConsulta("SELECT * FROM criptomonedas WHERE id = :id");
      $consulta->bindValue(':id', $id, PDO::PARAM_INT);
      $consulta->execute();
      $datosAux = $consulta->fetch(PDO::FETCH_BOTH);
      if ($datosAux) {
        return true;
      } else {
        return false;
      }
    }
 

}
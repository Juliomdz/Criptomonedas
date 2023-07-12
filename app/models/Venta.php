<?php
require_once './models/GestorDeArchivos.php';
require_once './models/Usuario.php';
require_once './models/Criptomoneda.php';

class Venta
{

    public $id;
    public $cantidad;
    public $fecha;
    public $foto;
    public $idUsuario;
    public $idCripto;

    public function crearVenta()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        

        $usuario = Usuario::obtenerUsuarioId($this->idUsuario);
        $cripto = Criptomoneda::obtenerCriptomonedaId($this->idCripto);
       
        if($usuario != false && $cripto != false)
        {
            $gestorArchivos = new GestorDeArchivos("../FotosCripto");
            $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO ventas (cantidad, fecha, foto, idUsuario, idCripto) 
            VALUES (:cantidad, :fecha, :foto, :idUsuario, :idCripto)");

            $nombreUsuario = explode("@", $usuario->mail)[0];

            $nuevaUrl=  "/" . $cripto->nombre . "_" . $nombreUsuario . "_" . $this->fecha . ".jpg";
            $this->foto = $nuevaUrl;
            

            $consulta->bindValue(':cantidad', $this->cantidad, PDO::PARAM_INT);
            $consulta->bindValue(':fecha', $this->fecha, PDO::PARAM_STR);
            $consulta->bindValue(':foto', $this->foto, PDO::PARAM_STR);
            $consulta->bindValue(':idUsuario', $this->idUsuario, PDO::PARAM_INT);
            $consulta->bindValue(':idCripto', $this->idCripto, PDO::PARAM_INT);
    
            $consulta->execute();
    
            $gestorArchivos->GuardarArchivoVenta($this->foto);
            return $objAccesoDatos->obtenerUltimoId();
        }



        
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, cantidad, fecha, foto, idUsuario, idCripto FROM ventas"); 
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Venta');
    }

    public static function obtenerTodosOrdenados($orden)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, cantidad, fecha, foto, idUsuario, idCripto
        FROM ventas
        ORDER BY cantidad $orden;"); 
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Venta');
    }

    public static function obtenerVentaId($id)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT  id, cantidad, fecha, foto, idUsuario, idCripto FROM ventas WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Venta');
    }

    public static function obtenerVentasPorNacionalidadEntreFechas($nacionalidad, $fecha1, $fecha2){

        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT venta.id, venta.cantidad, venta.fecha, venta.foto, venta.idUsuario, venta.idCripto FROM 
        `ventas` AS venta
        INNER JOIN `criptomonedas` AS cripto ON cripto.id=venta.idCripto AND cripto.nacionalidad = :nacionalidad
        WHERE venta.fecha BETWEEN :fecha1 AND :fecha2 ");
       
        $consulta->bindValue(':nacionalidad', $nacionalidad, PDO::PARAM_STR);
        $consulta->bindValue(':fecha1', $fecha1, PDO::PARAM_STR);
        $consulta->bindValue(':fecha2', $fecha2, PDO::PARAM_STR);

        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Venta');
    }

   
    public static function obtenerUsuariosPorNombreCripto($nombreCripto)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT u.mail, v.fecha, v.idUsuario , v.id, v.cantidad, 
        v.foto, v.idCripto FROM `ventas` as v
        INNER JOIN `usuarios` as u ON v.idUsuario = u.id INNER JOIN `criptomonedas` as c ON c.id = v.idCripto 
        WHERE c.nombre = :nombreCripto");

        $consulta->bindValue(':nombreCripto', $nombreCripto, PDO::PARAM_STR);
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Venta');

    }
    
}

?>
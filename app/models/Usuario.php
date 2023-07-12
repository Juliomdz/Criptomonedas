<?php

//mpc
class Usuario
{
    public $id;
    public $mail;
    public $tipo;
    public $clave;

    public function crearUsuario()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO usuarios (mail, tipo, clave) VALUES (:mail, :tipo, :clave)");
        $claveHash = password_hash($this->clave, PASSWORD_DEFAULT);
        $consulta->bindValue(':mail', $this->mail, PDO::PARAM_STR);
        $consulta->bindValue(':tipo', $this->tipo, PDO::PARAM_STR);
        $consulta->bindValue(':clave', $claveHash);
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, mail, tipo, clave FROM usuarios"); // WHERE fechaBaja IS NULL");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Usuario');
    }

    public static function obtenerUsuario($mail)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, mail, tipo, clave FROM usuarios WHERE mail = :mail");
        $consulta->bindValue(':mail', $mail, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Usuario');
    }

    public static function obtenerUsuarioId($id)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, mail, tipo, clave FROM usuarios WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Usuario');
    }



    public function modificarUsuario()
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE usuarios SET mail= :mail, tipo = :tipo, clave = :clave WHERE id = :id");
        $consulta->bindValue(':mail', $this->mail, PDO::PARAM_STR);
        $consulta->bindValue(':tipo', $this->tipo, PDO::PARAM_STR);
        $consulta->bindValue(':clave', $this->clave, PDO::PARAM_STR);

        return $consulta->execute();
    }

    
    public static function borrarUsuario($usuario)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE usuarios SET fechaBaja = :fechaBaja WHERE id = :id");
        $fecha = new DateTime(date("d-m-Y"));
        $consulta->bindValue(':id', $usuario, PDO::PARAM_INT);
        $consulta->bindValue(':fechaBaja', date_format($fecha, 'Y-m-d H:i:s'));
        return $consulta->execute();
    }
    

    /*
    public static function verificarId($id)
    {
      $objAccesoDato = AccesoDatos::obtenerInstancia();
      $consulta = $objAccesoDato->prepararConsulta("SELECT * FROM usuarios WHERE id = :id");
      $consulta->bindValue(':id', $id, PDO::PARAM_INT);
      $consulta->execute();
      $datosAux = $consulta->fetch(PDO::FETCH_BOTH);
      if ($datosAux) {
        return true;
      } else {
        return false;
      }
    }
 
    public static function verificarDatos($mail, $tipo, $clave) : int
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, mail, tipo, clave FROM usuarios WHERE mail = :mail");
        $consulta->bindValue(':mail', $mail, PDO::PARAM_STR);
        $consulta->execute();
        $retorno = 0;
        $userDataBase = $consulta->fetchObject('Usuario'); 

        if($userDataBase != null){
            if($userDataBase->mail == $mail){
                //comprueba q la contraseña coincida con un hash
                if(password_verify($clave,$userDataBase->clave) ||  $userDataBase->clave == $clave){
                    
                    $retorno = 1;
                }else{
                    var_dump($clave);
                    var_dump($userDataBase->clave);
                    $retorno = 2;
                }
            }
        }
        return $retorno;

    }
*/

}
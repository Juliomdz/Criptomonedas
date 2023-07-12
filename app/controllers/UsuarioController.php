<?php
require_once './models/Usuario.php';
require_once './interfaces/IApiUsable.php';
require_once './utils/AutentificadorJWT.php';

//mpc

class UsuarioController extends Usuario implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $mail = $parametros['mail'];
        $tipo = $parametros['tipo'];
        $clave = $parametros['clave'];

        $usr = new Usuario();
        $usr->mail = $mail;
        $usr->tipo = $tipo;
        $usr->clave = $clave;
        $usr->crearUsuario();

        $payload = json_encode(array("mensaje" => "Usuario creado con exito. Perfil:{$usr->tipo}"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerUno($request, $response, $args)
    {
        // Buscamos usuario por mail
        $mail = $args['mail'];
        $usuario = Usuario::obtenerUsuario($mail);
        $payload = json_encode($usuario);

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
        $lista = Usuario::obtenerTodos();
        $payload = json_encode(array("listaUsuario" => $lista));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
    
    public function ModificarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $nombre = $parametros['usuario'];
        $clave = $parametros['clave'];
        $usuario = new Usuario();
        $usuario->id = $args['usuarioId'];
        $usuario->nombre= $nombre;
        $usuario->clave = $clave;

        $usuario->ModificarUsuario();

        $payload = json_encode(array("mensaje" => "Usuario modificado con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    
    public function BorrarUno($request, $response, $args)
    {
      
          $parametros = $request->getParsedBody();

          $usuarioId = $args['usuarioId'];
    if (Usuario::verificarId($usuarioId)) {
      if (Usuario::borrarUsuario($usuarioId)) {
        $payload = json_encode(array("mensaje" => "Usuario borrado con exito"));
      } else {
        $payload = json_encode(array("mensaje" => "Error al borrar el usuario"));        
      }
    } else {
      $payload = json_encode(array("mensaje" => "ID inexistente"));
    }
    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
    }

  

  public function VerificarUsuario($request, $response, $args){

    $parametros = $request->getParsedBody();
    $mail = $parametros['mail'];
    $tipo = $parametros['tipo'];
    $clave = $parametros['clave'];

    $user = Usuario::obtenerUsuario($parametros['mail']);
    $payload = json_encode(array('status' => 'Invalid User'));
    
    if(!is_null($user)){


          if((password_verify($parametros['clave'],$user->clave) || $parametros['clave'] == $user->clave)){
            $userData = array(
                'id' => $user->id,
                'mail' => $user->mail,
                'tipo' => $user->tipo);
            $payload = json_encode(array('Token' => AutentificadorJWT::crearToken($userData), 'response' => 'OK', 'tipo:' => $user->tipo));
      }
        
    }
    
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
}
}

<?php
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

require_once './models/Usuario.php';

class LogginMiddleware
{
  public function __invoke(Request $request, RequestHandler $handler): Response
  {
    $response = new Response();
   
    $parametros = $request->getParsedBody();
   
    if (isset($parametros['clave']) && isset($parametros['mail']) && isset($parametros['tipo'])) {
      
      if ($parametros['clave'] == "" || $parametros['mail'] == "" || $parametros['tipo'] == "")
      {
        $response->getBody()->write("Error: hay campos vacios");
      }
      else
      {
        
        $response = $handler->handle($request);
      }
    } else {
      // aviso que faltaron datos
      $response->getBody()->write("Error: faltan enviar campos");
    }
    return $response;
  }
}
?>
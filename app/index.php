<?php
// Error Handling
error_reporting(-1);
ini_set('display_errors', 1);


use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;
use Slim\Routing\RouteContext;
use Slim\Logger;

require __DIR__ . '/../vendor/autoload.php';

require_once './db/AccesoDatos.php';

// require_once './middlewares/Logger.php';

require_once './controllers/UsuarioController.php';
require_once './middlewares/Loggin.php';
require_once './middlewares/VerificadorAdministrador.php';
require_once './middlewares/VerificadorDatos.php';
require_once './middlewares/VerificadorSuperAdmin.php';
require_once './middlewares/VerificadorId.php';
require_once './middlewares/Logger.php';
require_once './middlewares/VerificadorJWT.php';

require_once './middlewares/esAdminMiddleware.php';
require_once './middlewares/esUsuarioRegistradoMiddleware.php';
require_once './controllers/CriptomonedaController.php';
require_once './controllers/VentaController.php';
// Load ENV
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

// Instantiate App
$app = AppFactory::create();


// Add error middleware
$app->addErrorMiddleware(true, true, true);

// Add parse body
$app->addBodyParsingMiddleware();

//setPath
$app->setBasePath('/criptomonedas/app');
// Routes
/*CLASE ANTERIOR NO BORRAR*/
/*
$app->group('/usuarios', function (RouteCollectorProxy $group) {
    $group->get('[/]', \UsuarioController::class . ':TraerTodos');
    $group->get('/{usuario}', \UsuarioController::class . ':TraerUno');
    $group->post('[/]', \UsuarioController::class . ':CargarUno')->add(new VerificarAdministradorMiddleware())->add(new VerificarUsuarioYClaveMiddleware());
    $group->delete('/{usuarioId}', \UsuarioController::class . ':BorrarUno')->add(new VerificadorSuperAdminMiddleware()); //->add(new VerificadorIdMiddleware());
    $group->put('/{usuarioId}', \UsuarioController::class . ':ModificarUno');
  });
  */
// HOME
  $app->get('[/]', function (Request $request, Response $response) {    
    $response->getBody()->write("SEGUNDO PARCIAL - API ¨CRIPTOMONEDAS¨ - LABORATORIO III - MÉNDEZ JULIO");
    return $response;
});


$app->group('/usuarios', function (RouteCollectorProxy $group) {
  $group->get('[/]', \UsuarioController::class . ':TraerTodos');
  $group->post('/registrarse', \UsuarioController::class . ':CargarUno'); //dar de alta un cliente(registrarse)
  //PUNTO 1
  $group->post('/login', \UsuarioController::class . ':VerificarUsuario')->add(new LogginMiddleWare());  //iniciar sesion (verificarse)
});

$app->group('/criptomonedas', function (RouteCollectorProxy $group) {
  //PUNTO 2
  $group->post('[/]', \CriptomonedaController::class . ':CargarUno')
    ->add(new esAdminMiddleware());
    //PUNTO 3 y 4
    $group->get('[/]', \CriptomonedaController::class . ':TraerTodos');
    // PUNTO 11
    $group->get('/csv', \CriptomonedaController::class . ':TraerTodosCSV');
    // PUNTO 12
    $group->get('/logs/csv', \CriptomonedaController::class . ':TraerLogsCSV');
    //PUNTO 14
    $group->get('/logs/pdf', \CriptomonedaController::class . ':TraerLogsPDF');
    // PUNTO 5
    $group->get('/{id}', \CriptomonedaController::class . ':TraerUno')
    ->add(new esUsuarioRegistradoMiddleware());
    //PUNTO 16
    $group->get('/pdf/{id}', \CriptomonedaController::class . ':TraerUnoPDF');
//    ->add(new esUsuarioRegistradoMiddleware());
    //PUNTO 9
    $group->delete('/{criptoId}', \CriptomonedaController::class . ':BorrarUno')
    ->add(new esAdminMiddleware());
    //PUNTO 10
    $group->post('/{criptoId}', \CriptomonedaController::class . ':ModificarUno')
    ->add(new esAdminMiddleware());
});

$app->group('/ventas', function (RouteCollectorProxy $group) {
  $group->get('[/]', \VentaController::class . ':TraerTodos');
  //PUNTO 6
  $group->post('[/]', \VentaController::class . ':CargarUno')->add(new esUsuarioRegistradoMiddleware());
  //PUNTO 7
  $group->get('/{nacionalidad}/{fecha1}/{fecha2}', \VentaController::class . ':TraerTodosPorNacionalidad')
  ->add(new esAdminMiddleware());
  //PUNTO 8
  $group->get('/{nombreCripto}', \VentaController::class . ':TraerTodosPorCriptomoneda')->add(new esAdminMiddleware()); 
  //PUNTO 13
  $group->get('/ultimomes/{orden}', \VentaController::class . ':TraerUltimoMes'); 
  //PUNTO15
  $group->get('/ultimomesCSV/{orden}', \VentaController::class . ':TraerUltimoMesCSV'); 
});


$app->run();











































/*
$app->group('/jwt', function (RouteCollectorProxy $group) {

  $group->post('/loggin', \UsuarioController::class . ':Loggin')->add(new LogginMiddleWare());


});
*/
/*
$app->get(
  '[/]',
  function (Request $request, Response $response) {
    $payload = json_encode(array("metodo" => $_SERVER["REQUEST_METHOD"], "mensaje" => "Serena dematei"));        
    sleep(5);
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
  }
)->add(new LoggerMiddleware());

$app->post(
  '[/]',
  function (Request $request, Response $response) {    
    $payload = json_encode(array("metodo" => $_SERVER["REQUEST_METHOD"], "mensaje" => "Serena dematei"));    
    sleep(5);
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
  }
)->add(new LoggerMiddleware());
*/
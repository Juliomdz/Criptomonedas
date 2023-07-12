<?php
require_once './models/Venta.php';
require_once './interfaces/IApiUsable.php';
require_once './utils/AutentificadorJWT.php';
require_once './models/GestorDeArchivos.php';
require_once './models/Venta.php';

class VentaController extends Venta implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
    
        $parametros = $request->getParsedBody();
        $cantidad = $parametros['cantidad'];
        $fecha = $parametros['fecha'];
        $foto= $_FILES['foto']['name']; 
        $idUsuario = $parametros['idUsuario'];
        $idCripto = $parametros['idCripto'];
       

        
        $venta = new Venta();
        $venta->cantidad = $cantidad;
        $venta->fecha = $fecha;
        $venta->foto = $foto;
        $venta->idUsuario = $idUsuario;
        $venta->idCripto = $idCripto;
        $venta->CrearVenta();

        $payload = json_encode(array("mensaje" => "Venta creada con exito."));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }






    public function TraerUno($request, $response, $args)
    {
        
        $id = $args['id'];
        $venta = Venta::obtenerVentaId($id);
        $payload = json_encode($venta);

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
        $lista = Venta::obtenerTodos();
        $payload = json_encode(array("listaVentas" => $lista));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
    
    public function TraerTodosPorNacionalidad($request, $response, $args)
    {

      $nacionalidad = $args['nacionalidad'];
      $fecha1 = $args['fecha1'];
      $fecha2 = $args['fecha2'];

      $ventas = Venta::obtenerVentasPorNacionalidadEntreFechas($nacionalidad, $fecha1, $fecha2);
      if (!is_null($ventas)) {
        $payload = json_encode(array("Ventas: " => $ventas));
      }else{
          $payload = json_encode(array("Mensaje: " => "No existen ventas en esas fechas"));
      }

      $response->getBody()->write($payload);
      return $response->withHeader('Content-Type', 'application/json');
  }

  
  public function TraerTodosPorCriptomoneda($request, $response, $args)
  {

     $nombreCripto = $args['nombreCripto'];
     $lista = Venta::obtenerUsuariosPorNombreCripto($nombreCripto);
     if (!is_null($lista)) {
      $payload = json_encode(array("ListaUsuarios: " => $lista));
    }else{
        $payload = json_encode(array("Mensaje: " => "No existen usuarios que hayan comprado esas criptomonedas"));
    }

    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');

   
  }
  


    public function ModificarUno($request, $response, $args)
    {
        return 0;
    }

    public function BorrarUno($request, $response, $args)
    {
      return 0;
    }

    public function TraerUltimoMes($request, $response, $args)
    {
      $orden = $args['orden'];
      if ($orden == "ASC" || $orden == "DESC" ) {

        $lista = Venta::obtenerTodosOrdenados($orden);
        $payload = json_encode(array("listaVentas" => $lista));


        }

      else
      {
          $payload = json_encode(array("Mensaje: " => "Parametro invalido. Debe ser ASC o DESC"));

      }
      $response->getBody()->write($payload);
      return $response
        ->withHeader('Content-Type', 'application/json');
    }
    public function TraerUltimoMesCSV($request, $response, $args)
    {
      $orden = $args['orden'];
      if ($orden == "ASC" || $orden == "DESC") {
          $lista = Venta::obtenerTodosOrdenados($orden);
  
          $tempFile = fopen('php://temp', 'w');

          $columnas = ['id', 'cantidad', 'fecha','foto','idUsuario','idCripto']; 
          fputcsv($tempFile, $columnas);

          foreach ($lista as $fila) {
           fputcsv($tempFile, (array)$fila);
       }
       rewind($tempFile);

         // Configura las cabeceras de respuesta para indicar que se trata de un archivo CSV
         $response = $response->withHeader('Content-Type', 'text/csv')
                             ->withHeader('Content-Disposition', 'attachment; filename="criptomonedas.csv"');

         // Pasa el contenido del archivo al cliente como respuesta
         $response->getBody()->write(stream_get_contents($tempFile));

         // Cierra el archivo temporal
         fclose($tempFile);
         return $response;
      } else {
          $payload = json_encode(array("Mensaje: " => "Parámetro inválido. Debe ser ASC o DESC"));
          $response->getBody()->write($payload);
          return $response->withHeader('Content-Type', 'application/json');
      }
    }
   
    
}

?>
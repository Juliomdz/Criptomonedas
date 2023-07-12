<?php
require_once './models/Criptomoneda.php';
require_once './interfaces/IApiUsable.php';
require_once './utils/AutentificadorJWT.php';
require_once './models/GestorDeArchivos.php';
require_once './fpdf/fpdf.php';

class CriptomonedaController extends Criptomoneda implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
    
        $parametros = $request->getParsedBody();
        $precio= $parametros['precio'];
        $nombre = $parametros['nombre'];
        $nacionalidad = $parametros['nacionalidad'];
        $foto= $_FILES['foto']['name']; 

        $cripto = new Criptomoneda();
        $cripto->precio = $precio;
        $cripto->nombre = $nombre;
        $cripto->nacionalidad = $nacionalidad;
        $cripto->foto = $foto;
        $cripto->crearCriptomoneda();

        $payload = json_encode(array("mensaje" => "Criptomoneda creada con exito."));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }


    public function TraerUno($request, $response, $args)
    {
        
        $id= $args['id'];
        $cripto = Criptomoneda::obtenerCriptomonedaId($id);
        $payload = json_encode($cripto);

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerUnoPDF($request, $response, $args)
    {
      $id = $args['id'];
      $cripto = Criptomoneda::obtenerCriptomonedaId($id);
      $payload = json_encode($cripto, JSON_PRETTY_PRINT);
  
      // Crear instancia de FPDF
      $pdf = new FPDF();
      $pdf->AddPage();
  
      // Configurar fuente y tamaño
      $pdf->SetFont('Arial', '', 12);
  
      // Escribir el contenido del PDF
      $pdf->MultiCell(0, 10, $payload);
  
      // Generar el contenido del PDF y pasarlo como respuesta
      $response = $response->withHeader('Content-Type', 'application/pdf')
                           ->withHeader('Content-Disposition', 'attachment; filename="criptomoneda.pdf"');
      $response->getBody()->write($pdf->Output('S'));
  
      return $response;
    }

    public function TraerTodos($request, $response, $args)
    {
      
      if(isset($request->getQueryParams()['nacionalidad']))
      {
         $parametro = $request->getQueryParams()['nacionalidad'];
         
         if($parametro !=null)
         {
            $lista = Criptomoneda::obtenerTodosPorNacionalidad($parametro);
            $payload = json_encode(array("listaCriptomonedas" => $lista));
         }
         
      }
      else
      {
           $lista = Criptomoneda::obtenerTodos();
           $payload = json_encode(array("listaCriptomonedas" => $lista));
      }
      $response->getBody()->write($payload);
      return $response
        ->withHeader('Content-Type', 'application/json');




      /*
        $lista = Criptomoneda::obtenerTodos();
        $payload = json_encode(array("listaCriptomonedas" => $lista));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
          */
    }
    
    /*
    public function TraerTodosPorNacionalidad($request, $response, $args) {

        $nacionalidad = $args['nacionalidad'];

       // $parametros = $request->getParsedBody();
        //$nacionalidad = $parametros['nacionalidad'];

        $lista = Criptomoneda::obtenerTodosPorNacionalidad($nacionalidad);
        $payload = json_encode(array("listaCriptomonedas" => $lista));

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
    */


    public function BorrarUno($request, $response, $args)
    {
      
          $parametros = $request->getParsedBody();

          $criptoId = $args['criptoId'];
          if (Criptomoneda::verificarId($criptoId))
          {
            if (Criptomoneda::borrarCriptomoneda($criptoId))
            {
              $payload = json_encode(array("mensaje" => "Criptomoneda borrada con exito"));

              Criptomoneda::LogDeBorrado($criptoId);
            } 
          else
            {
              $payload = json_encode(array("mensaje" => "Error al borrar la criptomoneda"));        
            }
         } 
         else 
         {
          $payload = json_encode(array("mensaje" => "ID inexistente"));
         }
         $response->getBody()->write($payload);
         return $response->withHeader('Content-Type', 'application/json');
    }



    public function ModificarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $id = $args['criptoId'];
        $cripto = Criptomoneda::obtenerCriptomonedaId($id);
        
        
        $gestorArchivos = new GestorDeArchivos("./Backup_Cripto_Monedas_2023");
        $nombreArchivo=  "AltaCripto_" . $cripto->nombre .  ".jpg";


        if($gestorArchivos->archivoExistente("../FotosAltasCriptos",$nombreArchivo))  
        {
          
            if($gestorArchivos->moveImagen("../FotosAltasCriptos","./Backup_Cripto_Monedas_2023",  $nombreArchivo))
            {
              echo "La imagen fue removida a la carpeta Backup_Cripto_Monedas_2023";
            }
          
        }
        
       if($cripto!=null)
       {
        
          $cripto->precio = $parametros['precio'];
          $cripto->nombre = $parametros['nombre'];
          $cripto->nacionalidad = $parametros['nacionalidad'];
          $cripto->foto = $_FILES['foto']['name']; 
          $cripto->ModificarCriptomoneda();
          $payload = json_encode(array("mensaje" => "Criptomoneda modificada con exito"));

        }
        else
        {
          $payload = json_encode(array("mensaje" => "Id inexistente"));
        }

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
    
    public function TraerTodosCSV($request, $response, $args)
    {
           $lista = Criptomoneda::obtenerTodos();

           $tempFile = fopen('php://temp', 'w');

           $columnas = ['id', 'precio', 'nombre','nacionalidad', 'foto', 'fechaBaja']; 
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
    }

    public function TraerLogsCSV($request, $response, $args)
    {
           $lista = Criptomoneda::obtenerLogs();

           $tempFile = fopen('php://temp', 'w');

           $columnas = ['id_usuario', 'id_cripto', 'accion','fechaAccion']; 
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
    }

    public function TraerLogsPDF($request, $response, $args)
    {
      $lista = Criptomoneda::obtenerLogs();

      // Crear instancia de FPDF
      $pdf = new FPDF();
      $pdf->AddPage();
  
      // Configurar fuente y tamaño
      $pdf->SetFont('Arial', '', 12);
  
      // Iterar sobre la lista y escribir el contenido JSON en el PDF
      foreach ($lista as $fila) {
          $json = json_encode($fila, JSON_PRETTY_PRINT);
          $pdf->MultiCell(0, 10, $json);
      }
  
      // Generar el contenido del PDF y pasarlo como respuesta
      $response = $response->withHeader('Content-Type', 'application/pdf')
                           ->withHeader('Content-Disposition', 'attachment; filename="criptomonedasLOG.pdf"');
      $response->getBody()->write($pdf->Output('S'));
  
      return $response;
    }

}

?> 
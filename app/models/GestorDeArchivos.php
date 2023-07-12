<?php
require_once 'Criptomoneda.php';

class GestorDeArchivos
{
    private $ruta;

   

    public function __construct($ruta)
    {
        if (!file_exists($ruta)) {
            mkdir($ruta, 077, true);
        }
        $this->ruta = $ruta;
    }
 
  
    public function getRuta()
    {
        return $this->ruta;
    }

    
    public function setRuta($nuevaRuta)
    {
        $this->ruta = $nuevaRuta;
    }

    public function concatenarRuta($nombreArchivo)
    {
        return $this->getRuta() . $nombreArchivo;
    }
   
    public static function MoverImagen($origen, $destino, $nombreArchivo)
    {
       if (!file_exists($destino))
          mkdir($destino, 0777, true);
 
      var_dump($origen . $nombreArchivo);
      var_dump($destino . $nombreArchivo);
       return rename($origen . $nombreArchivo, $destino . $nombreArchivo);
    }

    
    public function moveImagen($pathFrom, $pathTo, $nombreArchivo)
    {
        if (isset($pathFrom) && isset($pathTo) && isset($nombreArchivo)) {
            $files = scandir($pathFrom);
            foreach ($files as $file) {
                if (strlen($file) > 2) {
                    if (
                        $file == $nombreArchivo
                        &&
                        file_exists($pathFrom . "/" . $file)
                    )
                    {
                        rename($pathFrom . "/" . $file, $pathTo ."/" . $file);
                        return true;

                    }
                }
            }
        }
        return false;
    }
    


    


    public function GuardarArchivoVenta($nombreArchivo)
    {
        $retorno = "Ocurrio un error al intentar guardar el archivo";
            if (
            $_FILES['foto']['type'] == 'image/jpeg' ||
            $_FILES['foto']['type'] == 'image/jpg' ||
            $_FILES['foto']['type'] == 'image/png') 
            {
                try
                {
                    move_uploaded_file($_FILES['foto']['tmp_name'], $this->concatenarRuta($nombreArchivo));
                    $retorno = "Archivo guardado con exito";
                }
                catch (Exception $e)
                {
                    echo 'Excepción capturada: ', $e->getMessage(), "\n";
                }
                finally
                {
                    return $retorno;
                }
              
            }
        
    }



    public function ArchivoExistente($pathFrom, $nombreFoto)
    {
        if(isset($pathFrom) && isset($nombreFoto))
        {
            
            $files = scandir($pathFrom);
            foreach($files as $f)
            {
                if(strlen($f)>2) 
                {
                    if(file_exists($pathFrom . "/" . $f))
                    {
                        return true;
                    }
                }
            }
        }
        return false;
    }
    

}



/*
class UploadManager{

    //--- Attributes ---//
    private $_DIR_TO_SAVE;
    private $_DIR_BACKUP;
    private $_fileExtension;
    private $_newFileName;
    private $_pathToSaveImage;

    //--- Constructor ---//
    public function __construct($dirToSave){
        $this->setDirectoryToSave($dirToSave);
        self::createDirIfNotExists($dirToSave);
    }
    
    private function printMessage($message, $variable){
        echo $message.' '.$variable.' <br>';
    }

    //--- Setters ---//

 
    public function setDirectoryToSave($dirToSave){
        $this->_DIR_TO_SAVE = $dirToSave;
    }

  
     
    public function setDirectoryBackup($dirBackup){
        $this->_DIR_BACKUP = $dirBackup;
    }


    public function setFileExtension($fileExtension = 'png'){
        $this->_fileExtension = $fileExtension;
    }


    public function setNewFileName($obj){
        $filename = 'None';
        if(is_a($obj, 'Sale')){
            $filename = $obj->getCryptoName().'_'.$obj->getCustomer().'_'.$obj->replaceDate();
        }else if(is_a($obj, 'Criptomoneda')){
            $filename = $obj->getName();
        }
        $this->_newFileName = $filename.'.'.$this->getFileExtension();
        
        //$this->printMessage('New file name', $this->_newFileName);
        
        $this->setPathToSaveImage();

        //$this->printMessage('Path to save image', $this->_pathToSaveImage);
    }


    public function setPathToSaveImage(){
        $this->_pathToSaveImage = $this->getDirectoryToSave().$this->getNewFileName();
    }
    
 
    public function getFileExtension(){
        return $this->_fileExtension;
    }

   
    public function getNewFileName(){
        return $this->_newFileName;
    }

    
    public function getPathToSaveImage(){
        return $this->_pathToSaveImage;
    }

   
    public function getDirectoryToSave(){
        return $this->_DIR_TO_SAVE;
    }

    
    public function getDirectoryBackup(){
        return $this->_DIR_BACKUP;
    }

    //--- Methods ---//

   
    public static function createDirIfNotExists($dirToSave){
        if (!file_exists($dirToSave)) {
            mkdir($dirToSave, 0777, true);
        }
    }

    public function saveFileIntoDir($obj, $FILES):bool{
        $success = false;
        
        try {
            $check = $this->checkIfExist($obj);
            if(empty($check)){
                $this->printMessage('File original', $FILES['foto']['tmp_name']); //decia image
                if ($this->moveUploadedFile($FILES['foto']['tmp_name'])) {
                    $success = true;
                }
            }else{
                self::moveImageFromTo($this->getDirectoryToSave(), $this->getDirectoryBackup(), $this->getNewFileName());
                $success = true;
            }
        } catch (\Throwable $th) {
            echo $th->getMessage();
        }finally{
            return $success;
        }
    }

    
    public function checkIfExist($obj){
        $this->setFileExtension();
        $this->setNewFileName($obj);
        $this->setPathToSaveImage();
        
        $this->printMessage('Path to save image', $this->getPathToSaveImage());
        $exist = file_exists($this->getPathToSaveImage());
        $this->printMessage('Exist: ', $exist);
        
        return $exist;
    }

    
    public function moveUploadedFile($tmpFileName){
        return move_uploaded_file($tmpFileName, $this->getPathToSaveImage());
    }

    
    public static function moveImageFromTo($oldDir, $newDir, $fileName){
        self::createDirIfNotExists($newDir);
        return rename($oldDir, $newDir.$fileName);
    }
}

?>
}
*/

?>
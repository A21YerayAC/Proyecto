<?php
namespace App\Controller;

use phpseclib3\Net\SFTP;
use Symfony\Component\Filesystem\Filesystem;

class ImageUploader
{
    private $sftp;

    public function __construct()
    {
      

$this->sftp = new SFTP("");

if (!$this->sftp->login("",  "")) {
    throw new \Exception('No se pudo conectar a SFTP');
}

    }

    public function uploadImage($image, $filename)
    {
        $image->move('/tmp', $filename);  // Mover la imagen a un directorio temporal en el servidor local
        $localFilePath = '/tmp/' . $filename;

        // Subir al droplet
        $remoteFilePath = '/var/www/html/imagenes/' . $filename;
        $this->sftp->put($remoteFilePath, $localFilePath, SFTP::SOURCE_LOCAL_FILE);

        // Eliminar el archivo temporal
        $filesystem = new Filesystem();
        $filesystem->remove($localFilePath);
    }
}


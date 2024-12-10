<?php
namespace App\Controller;

use phpseclib3\Net\SFTP;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class ImageUploader
{
    private $sftp;

    public function __construct(ParameterBagInterface $params)
    {
      

        $this->sftp = new SFTP($params->get('sftp_host'));

        if (!$this->sftp->login($params->get('sftp_user'), $params->get('sftp_password'))) {
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


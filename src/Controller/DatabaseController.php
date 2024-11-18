<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\DBAL\Connection;

class DatabaseController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    #[Route(path: '/checkconnection', name: 'app_check')]
    public function checkConnection(): Response
    {
        try {
            // Intenta realizar una consulta simple
            $this->entityManager->getConnection()->connect();
            return new Response('Conexión exitosa a la base de datos.');
        } catch (\Exception $e) {
            return new Response('Error de conexión: ' . $e->getMessage());
        }
    }

    
    #[Route(path: '/checkUser', name: 'check_user')]
public function userExists(): Response
{
    try {
        // Preparamos la consulta
        $sql = 'SELECT COUNT(*) FROM user WHERE email = :email';

        // Ejecutamos la consulta
        $stmt = $this->connection->prepare($sql);
        $result = $stmt->executeQuery(['email' => "yerayarosacasaldarnos@gmail.com"]);

        // Obtenemos el número de coincidencias
        $count = $result->fetchOne(); // Usamos fetchOne() para obtener el valor directamente

        // Si el conteo es mayor que 0, el usuario existe
        if ($count > 0) {
            return new Response('El usuario existe.');
        } else {
            return new Response('El usuario no existe.');
        }
    } catch (\Exception $e) {
        return new Response('Error al verificar el usuario: ' . $e->getMessage());
    }
}
}
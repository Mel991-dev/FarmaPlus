<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Database\Database;
use App\Models\ImagenProductoModel;
use App\Models\ProductoModel;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * ImagenProductoController — Gestión de imágenes adjuntas a productos.
 *
 * RF-IMG-01: Hasta 4 imágenes por producto (orden 1-4)
 * RF-IMG-02: Formatos aceptados: JPG, JPEG, PNG, WEBP · Máx 2 MB
 * RF-IMG-03: Se convierte a WebP cuando ext-gd con soporte WebP está disponible
 * RF-IMG-04: Almacenamiento en public/assets/uploads/productos/{id}/
 */
class ImagenProductoController
{
    private ImagenProductoModel $imagenModel;
    private ProductoModel $productoModel;

    public function __construct()
    {
        $db = Database::getInstance()->getConnection();
        $this->imagenModel   = new ImagenProductoModel($db);
        $this->productoModel = new ProductoModel($db);
    }

    /**
     * POST /inventario/productos/{id}/imagenes
     * Sube una imagen para el producto indicado.
     * Responde JSON {success, imagen_url, orden, imagen_id} o {error}.
     */
    public function upload(Request $request, Response $response, array $args): Response
    {
        $productoId = (int) $args['id'];
        $json = fn(array $data, int $status = 200) =>
            $response->withHeader('Content-Type', 'application/json')
                     ->withStatus($status)
                     ->getBody()->write(json_encode($data)) ?? $response;

        // Verificar que el producto existe
        $producto = $this->productoModel->obtenerPorId($productoId);
        if (!$producto) {
            $response->getBody()->write(json_encode(['error' => 'Producto no encontrado']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }

        // Verificar límite de 4 imágenes
        $total = $this->imagenModel->contarPorProducto($productoId);
        if ($total >= 4) {
            $response->getBody()->write(json_encode(['error' => 'El producto ya tiene el máximo de 4 imágenes']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(422);
        }

        // Leer archivo del request
        $uploadedFiles = $request->getUploadedFiles();
        $archivo = $uploadedFiles['imagen'] ?? null;

        if (!$archivo || $archivo->getError() !== UPLOAD_ERR_OK) {
            $response->getBody()->write(json_encode(['error' => 'No se recibió ningún archivo válido']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        // Validar tamaño (máx 2 MB)
        if ($archivo->getSize() > 2 * 1024 * 1024) {
            $response->getBody()->write(json_encode(['error' => 'El archivo supera el límite de 2 MB']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(422);
        }

        // Validar tipo MIME
        $mime = $archivo->getClientMediaType();
        $tiposPermitidos = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
        if (!in_array($mime, $tiposPermitidos, true)) {
            $response->getBody()->write(json_encode(['error' => 'Formato no permitido. Usa JPG, PNG o WEBP']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(422);
        }

        // Preparar directorio
        $uploadDir = __DIR__ . '/../../public/assets/uploads/productos/' . $productoId . '/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Determinar orden
        $orden = $total + 1;
        $nombreArchivo = 'img_' . $orden . '.webp';
        $rutaDestino   = $uploadDir . $nombreArchivo;

        // Convertir a WebP si GD lo soporta, si no guardar original
        $convertido = $this->convertirAWebP($archivo->getStream()->getMetadata('uri') ?? '', $mime, $rutaDestino);
        if (!$convertido) {
            // Fallback: guardar el archivo sin conversión con su extensión original
            $ext = pathinfo($archivo->getClientFilename(), PATHINFO_EXTENSION);
            $nombreArchivo = 'img_' . $orden . '.' . strtolower($ext);
            $rutaDestino   = $uploadDir . $nombreArchivo;
            $archivo->moveTo($rutaDestino);
        }

        // Guardar en BD
        $imagenId = $this->imagenModel->guardar($productoId, $nombreArchivo, $orden);

        $basePath = rtrim($_ENV['APP_BASEPATH'] ?? '', '/');
        $imagenUrl = $basePath . '/assets/uploads/productos/' . $productoId . '/' . $nombreArchivo;

        $response->getBody()->write(json_encode([
            'success'    => true,
            'imagen_id'  => $imagenId,
            'orden'      => $orden,
            'imagen_url' => $imagenUrl,
        ]));
        return $response->withHeader('Content-Type', 'application/json');
    }

    /**
     * DELETE /inventario/productos/{id}/imagenes/{imagenId}
     * Elimina una imagen del producto y reordena las restantes.
     */
    public function eliminar(Request $request, Response $response, array $args): Response
    {
        $productoId = (int) $args['id'];
        $imagenId   = (int) $args['imagenId'];

        $nombreArchivo = $this->imagenModel->eliminar($imagenId);

        if ($nombreArchivo !== '') {
            // Borrar el archivo físico
            $rutaFisica = __DIR__ . '/../../public/assets/uploads/productos/' . $productoId . '/' . $nombreArchivo;
            if (file_exists($rutaFisica)) {
                unlink($rutaFisica);
            }
        }

        $response->getBody()->write(json_encode(['success' => true]));
        return $response->withHeader('Content-Type', 'application/json');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Helpers privados
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Convierte un archivo de imagen a WebP usando ext-gd.
     * Retorna true si la conversión fue exitosa, false si hay que hacer fallback.
     */
    private function convertirAWebP(string $rutaOrigen, string $mime, string $rutaDestino): bool
    {
        if (!function_exists('imagewebp') || !$rutaOrigen || !file_exists($rutaOrigen)) {
            return false;
        }

        try {
            $img = match ($mime) {
                'image/jpeg', 'image/jpg' => imagecreatefromjpeg($rutaOrigen),
                'image/png'               => imagecreatefrompng($rutaOrigen),
                'image/webp'              => imagecreatefromwebp($rutaOrigen),
                default                   => false,
            };

            if (!$img) {
                return false;
            }

            // Redimensionar a máximo 800x800 manteniendo proporción
            $img = $this->redimensionar($img, 800, 800);

            $resultado = imagewebp($img, $rutaDestino, 82); // calidad 82%
            imagedestroy($img);
            return $resultado;
        } catch (\Throwable) {
            return false;
        }
    }

    /** Redimensionar una imagen GD manteniendo su relación de aspecto */
    private function redimensionar(\GdImage $img, int $maxW, int $maxH): \GdImage
    {
        $w = imagesx($img);
        $h = imagesy($img);

        if ($w <= $maxW && $h <= $maxH) {
            return $img; // Ya cabe, no es necesario redimensionar
        }

        $ratio  = min($maxW / $w, $maxH / $h);
        $newW   = (int) round($w * $ratio);
        $newH   = (int) round($h * $ratio);

        $nuevo = imagecreatetruecolor($newW, $newH);

        // Preservar transparencia para PNG
        imagealphablending($nuevo, false);
        imagesavealpha($nuevo, true);
        $transparente = imagecolorallocatealpha($nuevo, 0, 0, 0, 127);
        imagefilledrectangle($nuevo, 0, 0, $newW, $newH, $transparente);

        imagecopyresampled($nuevo, $img, 0, 0, 0, 0, $newW, $newH, $w, $h);
        imagedestroy($img);
        return $nuevo;
    }
}

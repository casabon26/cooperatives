<?php

// Provide a fallback for FILEINFO_MIME_TYPE constant when the PHP fileinfo
// extension is not available (prevents vendor libs from erroring).
if (!defined('FILEINFO_MIME_TYPE')) {
    define('FILEINFO_MIME_TYPE', 16);
}

// Polyfill for the built-in `finfo` class when the PHP fileinfo extension is not available.
// Provides minimal `file()` and `buffer()` methods using simple signature/extension checks.
if (!class_exists('finfo')) {
    class finfo
    {
        private $flags;
        private $magicFile;

        public function __construct($flags = 0, $magicFile = null)
        {
            $this->flags = $flags;
            $this->magicFile = $magicFile;
        }

        public function file($path)
        {
            if (!file_exists($path)) {
                return false;
            }
            $contents = @file_get_contents($path, false, null, 0, 512);
            return $this->buffer($contents) ?: $this->mimeFromExtension($path);
        }

        public function buffer($contents)
        {
            if ($contents === null || $contents === false) {
                return false;
            }
            // Basic signature checks for common image formats
            $s = substr($contents, 0, 16);
            if (strlen($s) >= 4 && substr($s, 0, 4) === "\x89PNG") {
                return 'image/png';
            }
            if (strlen($s) >= 3 && substr($s, 0, 3) === "GIF") {
                return 'image/gif';
            }
            if (strlen($s) >= 2 && substr($s, 0, 2) === "\xFF\xD8") {
                return 'image/jpeg';
            }
            if (stripos($s, 'webp') !== false) {
                return 'image/webp';
            }
            if (preg_match('/^\s*<\?xml|^\s*<svg/i', $s)) {
                return 'image/svg+xml';
            }
            // fallback
            return 'application/octet-stream';
        }

        private function mimeFromExtension($path)
        {
            $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
            $map = ['jpg'=>'image/jpeg','jpeg'=>'image/jpeg','png'=>'image/png','gif'=>'image/gif','webp'=>'image/webp','svg'=>'image/svg+xml','bmp'=>'image/bmp'];
            return $map[$ext] ?? 'application/octet-stream';
        }
    }
}

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();

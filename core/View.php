<?php

namespace Core;

class View {
    public static function make($viewName, $data = [])
    {
        extract($data);
        
        $viewPath = __DIR__ . '/../app/Http/Views/' . $viewName . '.php';
        if (file_exists($viewPath)) {
            require $viewPath;
        } else {
            throw new \Exception("View {$viewName} not found.");
        }
    }
}

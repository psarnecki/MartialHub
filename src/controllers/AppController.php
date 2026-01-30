<?php


class AppController {

    protected function isGet(): bool
    {
        return $_SERVER["REQUEST_METHOD"] === 'GET';
    }

    protected function isPost(): bool
    {
        return $_SERVER["REQUEST_METHOD"] === 'POST';
    }

    protected function getJsonData(array $requiredFields = []): array
    {
        $contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';

        if (strpos($contentType, "application/json") === false) {
            $this->terminateWithError(400);
        }

        $data = json_decode(trim(file_get_contents("php://input")), true);

        if ($data === null) {
            $this->terminateWithError(400);
        }

        foreach ($requiredFields as $field) {
            if (!isset($data[$field])) {
                $this->terminateWithError(400);
            }
        }

        return $data;
    }

    protected function render(string $template = null, array $variables = [])
    {
        $templatePath = 'public/views/'. $template.'.html';
        if(!file_exists($templatePath)){
            $this->terminateWithError(404);
        }
        $output = "";
                 
        extract($variables);
            
        ob_start();
        include $templatePath;
        $output = ob_get_clean();
        
        echo $output;
    }

    public function terminateWithError(int $code)
    {
        http_response_code($code);
        
        $view = (string)$code; 
        
        $this->render($view, ['errorCode' => $code]);
        exit;
    }
}
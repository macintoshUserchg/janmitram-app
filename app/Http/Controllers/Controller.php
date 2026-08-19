<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    protected function json(?string $message = null, $data = [], $statusCode = 200, array $headers = [])
    {
        $content = [];
        if ($message) {
            $content['message'] = $message;
        }

        if (! empty($data)) {
            $content['data'] = $data;
        }

        return response()->json($content, $statusCode, $headers, JSON_PRESERVE_ZERO_FRACTION);
    }

    protected function setEnv($key, $value)
    {
        try {
            $envFile = app()->environmentFilePath();
            if (! file_exists($envFile)) {
                return ['type' => 'error', 'message' => '.env file not found'];
            }
            $str = file_get_contents($envFile);

            $formattedValue = $value;
            if (is_string($value) && (preg_match('/\s|#|\$|"/', $value) || empty($value))) {
                $escaped = str_replace('"', '\"', $value);
                $formattedValue = "\"{$escaped}\"";
            }

            // Check if the key exists in the .env file
            if (preg_match("/^{$key}=.*/m", $str)) {
                $str = preg_replace("/^{$key}=.*/m", "{$key}={$formattedValue}", $str);
            } else {
                $str .= "\n{$key}={$formattedValue}\n";
            }

            // Update the .env file
            file_put_contents($envFile, $str);

            return ['type' => 'success', 'message' => __('Updated Successfully')];
        } catch (Exception $e) {
            return ['type' => 'error', 'message' => $e->getMessage()];
        }
    }
}

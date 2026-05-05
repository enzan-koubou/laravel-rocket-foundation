<?php
namespace EnzanRocket\Foundation\Http\Requests;

/**
 * Base request class for API endpoints.
 *
 * PUT/PATCH JSON body handling is natively supported by Laravel 9+ via
 * Symfony HttpFoundation — no third-party parser is needed.
 */
class APIRequest extends Request
{
    /**
     * @param string $key     the key
     * @param mixed  $default the default value if the parameter key does not exist
     *
     * @return mixed
     */
    public function get($key, $default = null): mixed
    {
        $data = parent::get($key, $default);

        // Support Android Retrofit Bad Data Format for multipart/form-data
        if (str_starts_with((string) request()->header('Content-Type'), 'multipart/form-data')) {
            if (str_starts_with((string) $data, 'Content')) {
                $pos = strpos((string) $data, "\r\n\r\n");
                if ($pos !== false) {
                    $data = substr((string) $data, $pos + 4);
                }
            }
        }

        return $data;
    }
}

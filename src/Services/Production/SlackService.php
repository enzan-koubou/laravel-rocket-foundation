<?php
namespace EnzanRocket\Foundation\Services\Production;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use EnzanRocket\Foundation\Services\SlackServiceInterface;

class SlackService extends BaseService implements SlackServiceInterface
{
    /**
     * Report an exception to slack.
     *
     * @param \Exception $e
     */
    public function exception(\Exception $e)
    {
        $fields = [];

        $addToField = function($name, $value, $short = false) use (&$fields) {
            if (!empty($value)) {
                $fields[] = [
                    'title' => $name,
                    'value' => $value,
                    'short' => $short,
                ];
            }
        };

        $addToField('Environment', app()->environment(), true);
        $addToField('Exception', get_class($e), true);
        $addToField(
            'Http code',
            $e instanceof \Symfony\Component\HttpKernel\Exception\HttpException ? $e->getStatusCode() : 500,
            true
        );
        $addToField('Code', $e->getCode(), true);
        $addToField('File', $e->getFile(), true);
        $addToField('Line', $e->getLine(), true);
        $addToField('Request url', request()->url(), true);
        $addToField('Request method', request()->method(), true);
        $addToField('Request param', json_encode(request()->all()), true);

        $message    = ':bug: Error Occurs on '.app()->environment();
        $type       = 'serious-alert';
        $pretext    = 'Error Occurs on '.app()->environment();
        $attachment = [
            'color'    => 'danger',
            'title'    => $e->getMessage(),
            'fallback' => !empty($e->getMessage()) ? $e->getMessage() : get_class($e),
            'pretext'  => $pretext,
            'fields'   => $fields,
            'text'     => $e->getTraceAsString(),
        ];

        // notify to slack
        $this->post($message, $type, $attachment);
    }

    /**
     * @param string $message
     * @param string $type
     * @param array  $attachment
     */
    public function post($message, $type, $attachment = [])
    {
        $typeConfig = config('slack.types.' . strtolower($type), config('slack.default', []));
        $webHookUrl = config('slack.webHookUrl');
        if (empty($webHookUrl)) {
            return;
        }

        $payload = [
            'username'   => Arr::get($typeConfig, 'username', 'EnzanRocketBot'),
            'channel'    => Arr::get($typeConfig, 'channel', '#random'),
            'icon_emoji' => Arr::get($typeConfig, 'icon', ':robot_face:'),
            'link_names' => true,
            'text'       => $message,
        ];

        if (!empty($attachment)) {
            $payload['attachments'] = [
                [
                    'fallback' => Arr::get($attachment, 'fallback', ''),
                    'text'     => Arr::get($attachment, 'text', ''),
                    'pretext'  => Arr::get($attachment, 'pretext', ''),
                    'color'    => Arr::get($attachment, 'color', 'good'),
                    'fields'   => Arr::get($attachment, 'fields', []),
                ],
            ];
        }

        Http::post($webHookUrl, $payload);
    }
}

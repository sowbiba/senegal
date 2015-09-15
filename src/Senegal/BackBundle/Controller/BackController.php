<?php

namespace Senegal\BackBundle\Controller;

use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Message\ResponseInterface;
use GuzzleHttp\Url;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class BackController extends Controller
{
    /**
     * Creates a fake array of {$total} entries, and add {$data} at offset {$offset}.
     * We use this system because of a performance issue during listing that cause the API to only return
     * a limited list and a field providing the total of entries in the database.
     *
     * @param array $data
     * @param $total
     * @param $offset
     *
     * @return array
     */
    public function getPaginateData(array $data, $total, $offset)
    {
        $array = new \SplFixedArray($total);

        $i = $offset;
        foreach ($data as $datum) {
            $array->offsetSet($i, $datum);

            $i++;
        }

        return $array->toArray();
    }

    protected function translate($id, array $parameters = [], $domain = null, $locale = null)
    {
        return $this->get('translator')->trans($id, $parameters, $domain, $locale);
    }

    /**
     * Send a GET request.
     *
     * @param string|array|Url $url     URL or URI template
     * @param array            $options Array of request options to apply.
     *
     * @return ResponseInterface
     *
     * @throws RequestException When an error is encountered
     */
    protected function apiGet($url = null, array $options = [])
    {
        return $this->get('senegal.api.client')->get($url, $this->getApiOptions($options));
    }

    /**
     * Send a DELETE request.
     *
     * @param string|array|Url $url     URL or URI template
     * @param array            $options Array of request options to apply.
     *
     * @return ResponseInterface
     *
     * @throws RequestException When an error is encountered
     */
    protected function apiDelete($url = null, array $options = [])
    {
        return $this->get('senegal.api.client')->delete($url, $this->getApiOptions($options));
    }

    /**
     * Send a POST request.
     *
     * @param string|array|Url $url     URL or URI template
     * @param array            $options Array of request options to apply.
     *
     * @return ResponseInterface
     *
     * @throws RequestException When an error is encountered
     */
    protected function apiPost($url = null, array $options = [])
    {
        return $this->get('senegal.api.client')->post($url, $this->getApiOptions($options));
    }

    /**
     * Send a PUT request.
     *
     * @param string|array|Url $url     URL or URI template
     * @param array            $options Array of request options to apply.
     *
     * @return ResponseInterface
     *
     * @throws RequestException When an error is encountered
     */
    protected function apiPut($url = null, array $options = [])
    {
        return $this->get('senegal.api.client')->put($url, $this->getApiOptions($options));
    }

    /**
     * Send a PATCH request.
     *
     * @param string|array|Url $url     URL or URI template
     * @param array            $options Array of request options to apply.
     *
     * @return ResponseInterface
     *
     * @throws RequestException When an error is encountered
     */
    protected function apiPatch($url = null, array $options = [])
    {
        return $this->get('senegal.api.client')->patch($url, $this->getApiOptions($options));
    }

    /**
     * @param array $options
     *
     * @return array
     */
    private function getApiOptions(array $options = [])
    {
        $token = $this->getUser() ? $this->getUser()->getToken() : null;

        return array_merge($options, ['headers' => ['api-key' => $token, 'referer' => $this->get('request')->getUri()]]);
    }
}

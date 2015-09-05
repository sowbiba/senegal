<?php

namespace Senegal\FrontBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class FrontController extends Controller
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

    protected function apiGet($url = null, array $options = [])
    {
        return $this->get('senegal_api_handler')->get($url, $this->getApiOptions($options));
    }

    protected function apiDelete($url = null, array $options = [])
    {
        return $this->get('senegal_api_handler')->delete($url, $this->getApiOptions($options));
    }

    protected function apiPost($url = null, array $options = [])
    {
        return $this->get('senegal_api_handler')->post($url, $this->getApiOptions($options));
    }

    protected function apiPut($url = null, array $options = [])
    {
        return $this->get('senegal_api_handler')->put($url, $this->getApiOptions($options));
    }

    protected function apiPatch($url = null, array $options = [])
    {
        return $this->get('senegal_api_handler')->patch($url, $this->getApiOptions($options));
    }

    private function getApiOptions(array $options = [])
    {
        return array_merge($options, ['headers' => ['api-key' => '']]); //$this->getUser()->getToken()
    }
}

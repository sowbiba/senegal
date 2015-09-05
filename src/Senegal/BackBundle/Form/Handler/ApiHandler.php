<?php

namespace Senegal\BackBundle\Form\Handler;

use Symfony\Component\Form\Form;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Bundle\TwigBundle\TwigEngine;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Translation\TranslatorInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Message\ResponseInterface;
use Symfony\Component\HttpFoundation\Response;
use GuzzleHttp\Exception\RequestException;

abstract class ApiHandler
{
    /**
     * @var Form
     */
    protected $form;

    /**
     * @var RequestStack
     */
    protected $requestStack;

    /**
     * @var TwigEngine
     */
    protected $templating;

    /**
     * @var Session
     */
    protected $session;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @var Client
     */
    protected $client;

    /**
     * @var TokenStorageInterface
     */
    protected $token;

    /**
     * @var string
     */
    protected $translateDomain;

    /**
     * @var string
     */
    protected $createMessage;

    /**
     * @var string
     */
    protected $updateMessage;

    /**
     * @param Form                  $form
     * @param RequestStack          $requestStack
     * @param TwigEngine            $templating
     * @param Session               $session
     * @param TranslatorInterface   $translator
     * @param Client                $client
     * @param TokenStorageInterface $token
     * @param array                 $options
     */
    public function __construct(
        Form $form,
        RequestStack $requestStack,
        TwigEngine $templating,
        Session $session,
        TranslatorInterface $translator,
        Client $client,
        TokenStorageInterface $token,
        array $options
    ) {
        $this->form = $form;
        $this->requestStack = $requestStack;
        $this->templating = $templating;
        $this->session = $session;
        $this->translator = $translator;
        $this->client = $client;
        $this->token = $token;

        list($this->translateDomain, $this->createMessage, $this->updateMessage) = $options;
    }

    /**
     * @param string      $httpMethod (must be 'post' or 'put')
     * @param string|null $apiUrl
     * @param array       $queries
     *
     * @return false|\stdClass
     */
    public function process($httpMethod, $apiUrl = null, array $queries = [])
    {
        $request = $this->requestStack->getCurrentRequest();

        if ('POST' === $request->getMethod()) {
            try {
                if (!$this->form->isSubmitted()) {
                    $this->form->submit($request);
                }

                if (in_array($httpMethod, ['post', 'put'])) {
                    return $this->onSuccess($this->client->$httpMethod($apiUrl,
                        [
                            'headers' => ['api-key' => ''], //$this->token->getToken()->getUser()->getToken()
                            'body' => array_merge($this->convertNullToEmptyString($this->form->getData()), $queries),
                        ]
                    ));
                }
            } catch (RequestException $e) {
                $this->session->getFlashBag()->add('error', $this->renderErrors(json_decode($e->getResponse()->getBody()->getContents(), true)));
            }
        }

        return false;
    }

    /**
     * @param ResponseInterface $response
     *
     * @return bool
     */
    protected function onSuccess(ResponseInterface $response)
    {
        $message = '';

        if (Response::HTTP_CREATED === $response->getStatusCode()) {
            $message = $this->translator->trans($this->createMessage, [], $this->translateDomain);
        } elseif (Response::HTTP_OK === $response->getStatusCode()) {
            $message = $this->translator->trans($this->updateMessage, [], $this->translateDomain);
        }

        $this->session->getFlashBag()->add('success', $message);

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * @param array $errors
     *
     * @return string
     *
     * @throws \Exception
     * @throws \Twig_Error
     */
    protected function renderErrors($errors)
    {
        return $this->templating->render('SenegalBackBundle:Templating:form-errors.html.twig',
            [
                'errors' => $errors,
                'translateDomain' => $this->translateDomain,
            ]
        );
    }

    /**
     * Convert, in an array, all null values to empty string ('').
     *
     * @param array $haystack
     *
     * @return mixed
     */
    private function convertNullToEmptyString(array $haystack)
    {
        if (!count($haystack)) {
            return '';
        }

        foreach ($haystack as $key => $value) {
            if (is_array($value)) {
                $haystack[$key] = $this->convertNullToEmptyString($haystack[$key]);
            }

            if (null === $haystack[$key]) {
                $haystack[$key] = '';
            }
        }

        return $haystack;
    }
}

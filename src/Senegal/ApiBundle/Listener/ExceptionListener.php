<?php

namespace Senegal\ApiBundle\Listener;

use Senegal\ApiBundle\Representation\VndErrorRepresentation;
use Senegal\ApiBundle\Utils\HashGenerator;
use FOS\RestBundle\View\View;
use JMS\Serializer\SerializationContext;
use Monolog\Logger;
use Pagerfanta\Exception\LessThan1CurrentPageException;
use Pagerfanta\Exception\LessThan1MaxPerPageException;
use Pagerfanta\Exception\NotIntegerCurrentPageException;
use Pagerfanta\Exception\NotIntegerMaxPerPageException;
use Pagerfanta\Exception\OutOfRangeCurrentPageException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Senegal\ApiBundle\Utils\Inflector;

class ExceptionListener
{
    const LOG_PREFIX = 'senegal.api';

    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();
        $logRef = $this->getLogRefPrefix($event->getRequest());

        $view = $this->handleException($exception, $logRef);

//        switch (true) {
//            case $exception instanceof NotIntegerMaxPerPageException:
//            case $exception instanceof LessThan1MaxPerPageException:
//            case $exception instanceof NotIntegerCurrentPageException:
//            case $exception instanceof LessThan1CurrentPageException:
//            case $exception instanceof OutOfRangeCurrentPageException:
//                $view = $this->handlePaginationException($exception, $logRef);
//                break;
//            case $exception instanceof NotValidFormException:
//                $view = $this->handleNotValidFormException($exception, $logRef);
//                break;
//            case $exception instanceof EntityNotFoundException:
//                $view = $this->handleEntityNotFoundException($exception, $logRef);
//                break;
//            case $exception instanceof EntityLockedException:
//                $view = $this->handleEntityLockedException($exception, $logRef);
//                break;
//            case $exception instanceof RevisionConflictException:
//                $view = $this->handleRevisionConflictException($exception, $logRef);
//                break;
//            default:
//                $view = $this->handleException($exception, $logRef);
//        }

        $event->setResponse(
            $this->container->get('fos_rest.view_handler')->handle($view, $event->getRequest())
        );
    }

    /**
     * Returns the log ref prefix depending of the request (_controller request parameter).
     * e.g. for MyTestController::superTestAction -> my_test.super_test.
     *
     * @param Request $request
     *
     * @return string
     */
    private function getLogRefPrefix(Request $request)
    {
        $logPrefix = 'application';

        if (null !== $requestController = $request->get('_controller')) {
            $requestController = explode('::', $requestController);

            $controller = Inflector::toUnderscoreCase(str_replace('Controller', '', substr($requestController[0], strrpos($requestController[0], '\\') + 1)));
            $action = Inflector::toUnderscoreCase(str_replace('Action', '', $requestController[1]));

            $logPrefix = "$controller.$action";
        }

        return $logPrefix;
    }

    /**
     * @param \Exception $exception
     * @param string     $logRef
     *
     * @return View
     */
    private function handleException(\Exception $exception, $logRef)
    {

        var_dump($exception->getMessage());
        $statusCode = Response::HTTP_BAD_REQUEST;

        $logRef = static::LOG_PREFIX.'.exception.'.$logRef.'.'.HashGenerator::generate();
        $errorMessage = new VndErrorRepresentation($exception->getMessage(), $logRef);

        $this->log($exception, Logger::WARNING, 'exception', $logRef, $statusCode, $errorMessage->toArray());

        return View::create($errorMessage, $statusCode)
            ->setSerializationContext(
                SerializationContext::create()->setSerializeNull(false)
            )
        ;
    }

    /**
     * @param \Exception $exception
     * @param string     $logRef
     *
     * @return View
     */
    private function handlePaginationException(\Exception $exception, $logRef)
    {
        $logRef = static::LOG_PREFIX.'.pagination.'.$logRef.'.'.HashGenerator::generate();

        switch (true) {
            case $exception instanceof NotIntegerMaxPerPageException:
                $data = new VndErrorRepresentation(
                    $this->container->get('translator')->trans('error.pagination.limit_parameter_is_not_integer', [], 'error'),
                    $logRef
                );
                $statusCode = Response::HTTP_BAD_REQUEST;
                break;
            case $exception instanceof LessThan1MaxPerPageException:
                $data = new VndErrorRepresentation(
                    $this->container->get('translator')->trans('error.pagination.limit_parameter_equal_to_zero', [], 'error'),
                    $logRef
                );
                $statusCode = Response::HTTP_BAD_REQUEST;
                break;
            case $exception instanceof NotIntegerCurrentPageException:
                $data = new VndErrorRepresentation(
                    $this->container->get('translator')->trans('error.pagination.page_parameter_is_not_integer', [], 'error'),
                    $logRef
                );
                $statusCode = Response::HTTP_BAD_REQUEST;
                break;
            case $exception instanceof LessThan1CurrentPageException:
                $data = new VndErrorRepresentation(
                    $this->container->get('translator')->trans('error.pagination.page_parameter_is_less_than_one', [], 'error'),
                    $logRef
                );
                $statusCode = Response::HTTP_BAD_REQUEST;
                break;
            case $exception instanceof OutOfRangeCurrentPageException:
                preg_match('/Page "(\d+)" does not exist. The currentPage must be inferior to "(\d+)"/', $exception->getMessage(), $matches);

                $data = new VndErrorRepresentation(
                    $this->container->get('translator')->trans(
                        'error.pagination.page_not_found',
                        ['%request_page%' => $matches[1], '%actual_page%' => $matches[2]],
                        'error'
                    ),
                    $logRef
                );
                $statusCode = Response::HTTP_NOT_FOUND;
                break;
            default:
                $data = new VndErrorRepresentation($exception->getMessage(), $logRef);
                $statusCode = Response::HTTP_BAD_REQUEST;
        }

        $this->log($exception, Logger::WARNING, 'pagination', $logRef, $statusCode, $data->toArray());

        return View::create($data, $statusCode)
            ->setSerializationContext(
                SerializationContext::create()->setSerializeNull(false)
            )
        ;
    }

    /**
     * @param EntityNotFoundException $exception
     * @param string                  $logRef
     *
     * @return View
     */
    private function handleEntityNotFoundException(EntityNotFoundException $exception, $logRef)
    {
        $statusCode = Response::HTTP_NOT_FOUND;

        $logRef = static::LOG_PREFIX.'.entity_not_found.'.$logRef.'.'.HashGenerator::generate();
        $errorMessage = new VndErrorRepresentation($this->container->get('translator')->trans('Not found', [], 'error'), $logRef);

        $this->log($exception, Logger::WARNING, 'entity_not_found', $logRef, $statusCode, $errorMessage->toArray());

        return View::create($errorMessage, $statusCode);
    }

    /**
     * @param EntityLockedException $exception
     * @param string                $logRef
     *
     * @return View
     */
    private function handleEntityLockedException(EntityLockedException $exception, $logRef)
    {
        $statusCode = Response::HTTP_LOCKED;

        $logRef = static::LOG_PREFIX.'.entity_locked.'.$logRef.'.'.HashGenerator::generate();
        $errorMessage = new VndErrorRepresentation($this->container->get('translator')->trans('Locked', [], 'error'), $logRef);

        $this->log($exception, Logger::WARNING, 'entity_locked', $logRef, $statusCode, $errorMessage->toArray());

        return View::create($errorMessage, $statusCode);
    }

    /**
     * @param NotValidFormException $exception
     * @param string                $logRef
     *
     * @return View
     */
    private function handleNotValidFormException(NotValidFormException $exception, $logRef)
    {
        $statusCode = Response::HTTP_UNPROCESSABLE_ENTITY;

        $form = $exception->getForm();

        $logRef = static::LOG_PREFIX.'.validation.'.$logRef.'.'.HashGenerator::generate();
        $errorMessage = $this->getFormErrors($form, $logRef);

        $extra = [];
        foreach ($form->all() as $fieldName => $field) {
            $extra['formData'][$fieldName] = $field->getData();
        }

        $this->log($exception, Logger::INFO, 'validation', $logRef, $statusCode, $errorMessage->toArray(), $extra);

        return View::create($errorMessage, $statusCode)
            ->setSerializationContext(
                SerializationContext::create()->setSerializeNull(false)
            )
        ;
    }

    /**
     * @param RevisionConflictException $exception
     * @param string                    $logRef
     *
     * @return object|View
     */
    private function handleRevisionConflictException(RevisionConflictException $exception, $logRef)
    {
        $statusCode = Response::HTTP_CONFLICT;

        $logRef = static::LOG_PREFIX.'.conflict.'.$logRef.'.'.HashGenerator::generate();
        $errorMessage = $this->getConflictErrors($exception->getDifferences(), $logRef);

        if (null === $errorMessage) {
            return $exception->getSubmittedEntity();
        }

        $extra = [
            'current_values' => $exception->getCurrentEntity()->getConcurrencyArray(),
            'submitted_values' => $exception->getSubmittedEntity()->getConcurrencyArray(),
        ];

        $this->log($exception, Logger::ERROR, 'conflict', $logRef, $statusCode, $errorMessage->toArray(), $extra);

        return View::create($errorMessage, $statusCode)
            ->setSerializationContext(
                SerializationContext::create()->setSerializeNull(true)
            )
        ;
    }

    /**
     * @param Form   $form
     * @param string $logRef
     *
     * @return VndErrorCollectionRepresentation
     */
    private function getFormErrors(Form $form, $logRef)
    {
        $formErrors = FormErrorsSerializer::serializeFormErrors($form);

        $translator = $this->container->get('translator');

        // The translation file should be define by following this naming convention : 'entity', 'entity_category'.
        $dataClass = $form->getConfig()->getDataClass();
        $translationDomain = Inflector::toUnderscoreCase(substr($dataClass, strrpos($dataClass, '\\') + 1));

        $errors = [];
        foreach ($formErrors['global'] as $error) {
            $message = $translator->trans($error['message'], [], $translationDomain);
            if ($message === $error['message']) {
                $message = $translator->trans($error['message'], [], 'error');
            }

            $errors[] = new VndErrorValidationRepresentation($message, $error['message'], $error['code'], null);
        }

        foreach ($formErrors['fields'] as $field => $error) {
            $message = $translator->trans($error['message'], [], $translationDomain);
            if ($message === $error['message']) {
                $message = $translator->trans($error['message'], [], 'error');
            }

            $errors[] = new VndErrorValidationRepresentation($message, $error['message'], $error['code'], $field);
        }

        return new VndErrorCollectionRepresentation(
            $this->container->get('translator')->trans('Validation failed', [], 'error'), $errors, $logRef
        );
    }

    /**
     * @param array  $differences
     * @param string $logRef
     *
     * @return VndErrorCollectionRepresentation
     */
    private function getConflictErrors(array $differences, $logRef)
    {
        $errors = [];
        foreach ($differences as $field => $diffs) {
            $errors[] = new VndErrorConflictRepresentation($field, $diffs[0], $diffs[1]);
        }

        return new VndErrorCollectionRepresentation(
            $this->container->get('translator')->trans('Conflicts', [], 'error'), $errors, $logRef
        );
    }

    /**
     * @param \Exception $exception
     * @param string     $recordType
     * @param string     $type
     * @param string     $logRef
     * @param int        $statusCode
     * @param array      $errorMessages
     * @param array      $extra
     */
    private function log(\Exception $exception, $recordType, $type, $logRef, $statusCode, array $errorMessages, array $extra = [])
    {
        $user = null;
        if (null !== $token = $this->container->get('security.token_storage')->getToken()) {
            $user = $token->getUser();
        }

        $request = $this->container->get('request');
        $extra = array_merge($extra, [
            'trace' => $this->getExceptionTrace($exception),
            'uri' => $request->getUri(),
            'method' => $request->getMethod(),
            'route_name' => $request->get('_route'),
        ]);

        $this->container->get('senegal.app.logger')->addRecord(
            $recordType,
            json_encode(
                [
                    'ref' => $logRef,
                    'type' => $type,
                    'status_code' => $statusCode,
                    'referer' => $this->container->get('request')->headers->get('referer'),
                    'user' => is_object($user) ? $user->getId() : null,
                    'error_message' => $errorMessages,
                    'extra' => $extra,
                ]
            )
        );
    }

    /**
     * @param \Exception $exception
     *
     * @return array
     */
    private function getExceptionTrace(\Exception $exception)
    {
        return array_values(array_filter(explode('#', $exception->getTraceAsString())));
    }
}

<?php

namespace Senegal\Api\SdkBundle\Request\ParamConverter;

use Pfd\Sdk\Mediator\MediatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ConfigurationInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;

/**
 * ModelParamConverter.
 */
class ModelParamConverter implements ParamConverterInterface
{
    /**
     * @var Sdk
     */
    protected $mediator;

    /**
     * @param SdkMediator $mediator
     */
    public function __construct(MediatorInterface $mediator)
    {
        $this->mediator = $mediator;
    }

    /**
     * @{inheritdoc}
     *
     * @throws \LogicException       When unable to guess how to get a Doctrine instance from the request information
     * @throws NotFoundHttpException When object not found
     */
    public function apply(Request $request, ParamConverter $configuration)
    {
        $name    = $configuration->getName();
        $class   = $configuration->getClass();

        if (null === $request->attributes->get($name, false)) {
            $configuration->setIsOptional(true);
        }

        // find the object
        $identifier = $this->getIdentifier($request, $configuration);
        $object = null;

        if (null !== $identifier) {
            $object = $this->mediator->getSdkByClass($class)->getById((int) $identifier);
        }

        if (null === $object && false === $configuration->isOptional()) {
            throw new NotFoundHttpException(sprintf('%s object not found.', $class));
        }

        $request->attributes->set($name, $object);

        return true;
    }

    /**
     * @param Request $request
     * @param         $options
     * @param         $name
     *
     * @return array|bool|mixed
     */
    protected function getIdentifier(Request $request, ConfigurationInterface $configuration)
    {
        $options = $configuration->getOptions();
        $name = $configuration->getName();

        if (isset($options['id'])) {
            if (!is_array($options['id'])) {
                $name = $options['id'];
            } elseif (is_array($options['id'])) {
                throw new \LogicException("Multiple id");
            }
        }

        if ($request->attributes->has($name)) {
            return $request->attributes->get($name);
        }

        if ($request->attributes->has('id')) {
            return $request->attributes->get('id');
        }

        if (!$configuration->isOptional()) {
            throw new \LogicException('Unable to guess how to get a sdk model instance from the request information.');
        }

        return null;
    }

    /**
     * @param ParamConverter $configuration
     *
     * @return bool
     */
    public function supports(ParamConverter $configuration)
    {
        if (null === ($classname = $configuration->getClass())) {
            return false;
        }

        if (!class_exists($classname)) {
            return false;
        }

        $class = new \ReflectionClass($configuration->getClass());

        return $class->isSubclassOf('\Pfd\Sdk\Model\BaseModel');
    }
}

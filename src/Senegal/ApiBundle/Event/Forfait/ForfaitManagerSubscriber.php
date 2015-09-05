<?php

namespace Senegal\ApiBundle\Event\Forfait;

use Senegal\ApiBundle\Entity\Forfait;
use Senegal\ApiBundle\Entity\ForfaitHasTypePage;
use Senegal\ApiBundle\Event\ApiEvents;
use Senegal\ApiBundle\Event\FilterManagerEvent;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ForfaitManagerSubscriber implements EventSubscriberInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public static function getSubscribedEvents()
    {
        return [
            ApiEvents::PRE_MANAGER_FORFAIT_INSERT    => ['onPreManagerForfaitInsertUpdate', 0],
            ApiEvents::PRE_MANAGER_FORFAIT_UPDATE    => ['onPreManagerForfaitInsertUpdate', 0],
        ];
    }

    public function onPreManagerForfaitInsertUpdate(FilterManagerEvent $event)
    {
        $data = $event->getData();
        /** @var Forfait $forfait     */
        $forfait = $event->getEntity();

        if (isset($data['forfaitTypePages'])) {
            $forfaitTypePages = [];
            foreach ($data['forfaitTypePages'] as $forfaitTypePage) {
                if (isset($forfaitTypePage['typePage']) && !empty($forfaitTypePage['typePage'])) {
                    $typePage = $this->container->get('senegal_type_page_manager')->find($forfaitTypePage['typePage']);
                    if (null === $typePage) {
                        continue;
                    }

                    $newForfaitTypePage = $this->container->get('senegal_forfait_type_page_manager')->findByFilters(['forfait' => $forfait->getId(), 'typePage' => intval($forfaitTypePage['typePage'])]);
                    if (null === $newForfaitTypePage) {
                        $newForfaitTypePage = new ForfaitHasTypePage();
                    }

                    $newForfaitTypePage->setForfait($forfait);
                    $newForfaitTypePage->setTypePage($typePage);

                    if (isset($forfaitTypePage['allowedPageNumber']) && (null !== $forfaitTypePage['allowedPageNumber'])) {
                        $newForfaitTypePage->setAllowedPageNumber($forfaitTypePage['allowedPageNumber']);
                    }

                    $forfaitTypePages[] = $newForfaitTypePage;
                }
            }

            $data['forfaitTypePages'] = $forfaitTypePages;
        }

        $event->setData($data);
    }
}

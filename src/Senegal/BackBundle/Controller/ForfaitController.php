<?php

namespace Senegal\BackBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View as FOSView;
use JMS\Serializer\SerializationContext;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Senegal\ApiBundle\Entity\forfait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * @Rest\NamePrefix("forfait_")
 */
class ForfaitController extends BackController
{
    /**
     * @Route("/forfaits", name="senegal_back_forfaits_list")
     *
     * @param  Request  $request
     * @return Response
     */
    public function listAction(Request $request)
    {
        $page = $request->query->get('page', 1);

        $filters['sortField'] = $request->query->get('sort', 'role');
        $filters['sortOrder'] = $request->query->get('direction', 'asc');
        $filters['limit'] = 20;
        $filters['offset'] = $filters['limit'] * ($page - 1);

        $forfaits = $this->get('senegal.api.client')->get('forfaits', ['query' => $filters])->json();

        $forfaits_pagination = $this->get('knp_paginator')->paginate(
            (isset($forfaits['forfaits']) && isset($forfaits['total'])) ? $this->getPaginateData($forfaits['forfaits'], $forfaits['total'], $filters['offset']) : [],
            $page,
            $filters['limit']
        );

        return $this->render('SenegalBackBundle:Forfait:list.html.twig', [
            'forfaits' => $forfaits_pagination
        ]);
    }

    /**
     * @Route("/forfait/create", name="senegal_back_forfaits_create")
     *
     * @param  Request  $request
     * @return Response
     * @throws HttpException
     */
    public function createAction(Request $request)
    {
        $form = $this->get('senegal.forfait.form');

        try {
            $forfait = $this->get('senegal.forfait.form.handler')->process('post', 'forfait');

            if ($forfait) {
                $url = $this->generateUrl('senegal_back_forfaits_list');

                if ($request->request->has('create_and_edit')) {
                    $url = $this->generateUrl('senegal_back_forfait_edit', ['forfaitId' => $forfait['id']]);
                } elseif ($request->request->has('create_and_create')) {
                    $url = $this->generateUrl('senegal_back_forfaits_create');
                }

                return $this->redirect($url);
            }
        } catch (RequestException $e) {
            throw new HttpException($e->getResponse()->getStatusCode(), $e->getResponse()->getBody()->getContents());
        }

        return $this->render('SenegalBackBundle:Forfait:create.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/forfait/{forfaitId}/edit", name="senegal_back_forfait_edit", requirements={"forfaitId"="\d+"})
     *
     * @param Request $request
     * @param int     $forfaitId
     *
     * @return Response
     */
    public function editAction(Request $request, $forfaitId)
    {
        $creator = [];

        $form = $this->get('senegal.forfait.form');

        try {
            $forfait = $this->apiGet("forfait/$forfaitId")->json();

            $form->setData($forfait);

            $postQueries = [];

            $forfait = $this->get('senegal.forfait.form.handler')->process('put', "forfait/$forfaitId", $postQueries);

            if ($forfait) {
                $url = $this->generateUrl('senegal_back_forfaits_list');

                if ($request->request->has('update_and_edit')) {
                    $url = $this->generateUrl('senegal_back_forfait_edit', ['forfaitId' => $forfaitId]);
                }

                return $this->redirect($url);
            }
        } catch (RequestException $e) {
            throw new HttpException($e->getResponse()->getStatusCode(), $e->getResponse()->getBody()->getContents());
        }

        return $this->render('SenegalBackBundle:Forfait:edit.html.twig',
            [
                'form'      => $form->createView()
            ]
        );
    }
}

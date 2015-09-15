<?php

namespace Senegal\BackBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View as FOSView;
use JMS\Serializer\SerializationContext;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Senegal\ApiBundle\Entity\typePage;
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
 * @Rest\NamePrefix("type_page_")
 */
class TypePageController extends BackController
{
    /**
     * @Route("/type-pages", name="senegal_back_type_pages_list")
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

        $typePages = $this->get('senegal.api.client')->get('type-pages', ['query' => $filters])->json();

        $typePages_pagination = $this->get('knp_paginator')->paginate(
            (isset($typePages['typePages']) && isset($typePages['total'])) ? $this->getPaginateData($typePages['typePages'], $typePages['total'], $filters['offset']) : [],
            $page,
            $filters['limit']
        );

        return $this->render('SenegalBackBundle:TypePage:list.html.twig', [
            'typePages' => $typePages_pagination
        ]);
    }

    /**
     * @Route("/type-page/create", name="senegal_back_type_page_create")
     *
     * @param  Request  $request
     * @return Response
     * @throws HttpException
     */
    public function createAction(Request $request)
    {
        $form = $this->get('senegal.type_page.form');

        try {
            $typePage = $this->get('senegal.type_page.form.handler')->process('post', 'type-page');

            if ($typePage) {
                $url = $this->generateUrl('senegal_back_type_pages_list');

                if ($request->request->has('create_and_edit')) {
                    $url = $this->generateUrl('senegal_back_type_page_edit', ['typePageId' => $typePage['id']]);
                } elseif ($request->request->has('create_and_create')) {
                    $url = $this->generateUrl('senegal_back_type_page_create');
                }

                return $this->redirect($url);
            }
        } catch (RequestException $e) {
            throw new HttpException($e->getResponse()->getStatusCode(), $e->getResponse()->getBody()->getContents());
        }

        return $this->render('SenegalBackBundle:TypePage:create.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/type-page/{typePageId}/edit", name="senegal_back_type_page_edit", requirements={"typePageId"="\d+"})
     *
     * @param Request $request
     * @param int     $typePageId
     *
     * @return Response
     */
    public function editAction(Request $request, $typePageId)
    {
        $form = $this->get('senegal.type_page.form');

        try {
            $typePage = $this->apiGet("type-page/$typePageId")->json();

            $form->setData($typePage);

            $postQueries = [];

            $typePage = $this->get('senegal.type_page.form.handler')->process('put', "type-page/$typePageId", $postQueries);

            if ($typePage) {
                $url = $this->generateUrl('senegal_back_type_pages_list');

                if ($request->request->has('update_and_edit') || $request->request->has('update_and_edit_and_force')) {
                    $url = $this->generateUrl('senegal_back_type_page_edit', ['typePageId' => $typePageId]);
                }

                return $this->redirect($url);
            }
        } catch (RequestException $e) {
            throw new HttpException($e->getResponse()->getStatusCode(), $e->getResponse()->getBody()->getContents());
        }

        return $this->render('SenegalBackBundle:TypePage:edit.html.twig',
            [
                'form'      => $form->createView()
            ]
        );
    }

    /**
     * @Route("/type-page/{typePageId}/delete", name="senegal_back_type_page_delete", requirements={"typePageId"="\d+"})
     *
     * @param Request $request
     * @param int     $typePageId
     *
     * @return Response
     */
    public function deleteAction(Request $request, $typePageId)
    {
        if ('1' !== $request->query->get('modal-confirm')) {
            throw $this->createNotFoundException();
        }

        try {
            $this->apiDelete("type-page/$typePageId");

            $this->addFlash('success', $this->translate('messages.delete_success', [], 'back_type_pages'));
        } catch (RequestException $e) {
            throw new HttpException($e->getResponse()->getStatusCode(), $e->getResponse()->getBody()->getContents());
        }

        return $this->redirect($this->generateUrl('senegal_back_type_pages_list'));
    }
}

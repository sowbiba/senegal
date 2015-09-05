<?php

namespace Senegal\BackBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View as FOSView;
use JMS\Serializer\SerializationContext;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Senegal\ApiBundle\Entity\User;
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
 * @Rest\NamePrefix("user_")
 */
class UserController extends BackController
{
    /**
     * @Route("/users", name="senegal_back_users_list")
     *
     * @param  Request  $request
     * @return Response
     */
    public function listAction(Request $request)
    {
        $filterForm = $this->get('senegal.user_filter.form');

        $filters = [];
        if ($request->query->has('user_filter')) {
            $filters = $request->query->get('user_filter');

            $filterForm->setData($filters);
        }

        $page = $request->query->get('page', 1);

        $filters['sortField'] = $request->query->get('sort', 'role');
        $filters['sortOrder'] = $request->query->get('direction', 'asc');
        $filters['limit'] = 20;
        $filters['offset'] = $filters['limit'] * ($page - 1);

        $users = $this->get('senegal_api_handler')->get('users', ['query' => $filters])->json();

        $users_pagination = $this->get('knp_paginator')->paginate(
            (isset($users['users']) && isset($users['total'])) ? $this->getPaginateData($users['users'], $users['total'], $filters['offset']) : [],
            $page,
            $filters['limit']
        );

        return $this->render('SenegalBackBundle:User:list.html.twig', [
            'users' => $users_pagination,
            'filter_form' => $filterForm->createView(),
        ]);
    }

    /**
     * @Route("/user/create", name="senegal_back_users_create")
     *
     * @param  Request  $request
     * @return Response
     * @throws HttpException
     */
    public function createAction(Request $request)
    {
        $form = $this->get('senegal.user.form');

        try {
            $user = $this->get('senegal.user.form.handler')->process('post', 'user');

            if ($user) {
                $url = $this->generateUrl('senegal_back_users_list');

                if ($request->request->has('create_and_edit')) {
                    $url = $this->generateUrl('senegal_back_users_edit', ['userId' => $user['id']]);
                } elseif ($request->request->has('create_and_create')) {
                    $url = $this->generateUrl('senegal_back_users_create');
                }

                return $this->redirect($url);
            }
        } catch (RequestException $e) {
            throw new HttpException($e->getResponse()->getStatusCode(), $e->getResponse()->getBody()->getContents());
        }

        return $this->render('SenegalBackBundle:User:create.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/user/{userId}/edit", name="senegal_back_users_edit", requirements={"userId"="\d+"})
     *
     * @param Request $request
     * @param int     $userId
     *
     * @return Response
     */
    public function editAction(Request $request, $userId)
    {
        $creator = [];

        $form = $this->get('senegal.user.form');

        try {
            $user = $this->apiGet("user/$userId")->json();

            $form->setData($user);

            $postQueries = [];

            $user = $this->get('senegal.user.form.handler')->process('put', "user/$userId", $postQueries);

            if ($user) {
                $url = $this->generateUrl('senegal_back_users_list');

                if ($request->request->has('update_and_edit') || $request->request->has('update_and_edit_and_force')) {
                    $url = $this->generateUrl('senegal_back_users_edit', ['userId' => $userId]);
                }

                return $this->redirect($url);
            }
        } catch (RequestException $e) {
            throw new HttpException($e->getResponse()->getStatusCode(), $e->getResponse()->getBody()->getContents());
        }

        return $this->render('SenegalBackBundle:User:edit.html.twig',
            [
                'form'      => $form->createView()
            ]
        );
    }

    /**
     * @Route("/users/export", name="pfd_broadcast_back_users_export")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function exportAction(Request $request)
    {
        $query = [];

        if ($request->query->has('user_filter')) {
            $query = $request->query->get('user_filter');
        }

        $query['sortField'] = $request->query->get('sort', 'updatedAt');
        $query['sortOrder'] = $request->query->get('direction', 'desc');

        $users = $this->apiGet('users', ['query' => $query])->json();

        return $this->get('pfd.export.users')->send('export_utilisateurs.xlsx', isset($users['users']) && count($users['users']) ? $users['users'] : []);
    }

    /**
     * @Route("/user/{userId}/change-mail-receive-status", name="pfd_broadcast_back_users_change_mailreceive_status", requirements={"userId" = "\d+"})
     *
     * @param  Request  $request
     * @param $userId
     * @return Response
     */
    public function changeMailReceiveStatusAction(Request $request, $userId)
    {
        $data = '';

        if ('POST' === $request->getMethod()) {
            try {
                //Check if no other user have locked the object
                $user = $this->apiGet("user/$userId")->json();
                $lockData = isset($user['lockedBy']) && isset($user['lockedAt']) ? ['name' => $user['lockedBy'], 'date' => $user['lockedAt']] : null;

                if (null !== $lockData) {
                    return new JsonResponse(['locked' => true]);
                }

                $apiUrl = "user/$userId";
                $status = $request->request->get('receiveMail', null);

                if ('true' === $status) {
                    $this->apiPut($apiUrl, ['body' => ['receiveMail' => true]]);
                } elseif ('false' === $status) {
                    $this->apiPut($apiUrl, ['body' => ['receiveMail' => false]]);
                } else {
                    throw new \Exception();
                }

                $data = ['success' => true];
            } catch (\Exception $e) {
                $data = ['success' => false];
            }
        }

        return new JsonResponse($data);
    }

    /**
     * @Route("/user/{userId}/change-activation-status", name="senegal_back_users_change_activation_status", requirements={"userId" = "\d+"})
     *
     * @param  Request  $request
     * @param $userId
     * @return Response
     */
    public function changeActivationStatusAction(Request $request, $userId)
    {
        $data = '';

        if ('POST' === $request->getMethod()) {
            try {
                //Check if no other user have locked the object
                $user = $this->apiGet("user/$userId")->json();

                $apiUrl = "user/$userId";
                $status = $request->request->get('active', null);

                if ('true' === $status) {
                    $this->apiPut($apiUrl, ['body' => ['active' => true]]);
                } elseif ('false' === $status) {
                    $this->apiPut($apiUrl, ['body' => ['active' => false]]);
                } else {
                    throw new \Exception();
                }

                $data = ['success' => true];
            } catch (\Exception $e) {
                $data = ['success' => false];
            }
        }

        return new JsonResponse($data);
    }
}

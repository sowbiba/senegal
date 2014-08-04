<?php
namespace Api\AuthenticationBundle\Tests\Controller;

use Api\Sdk\Model\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class AuthControllerTest extends WebTestCase
{
    public function loginCheckDataProvider()
    {
        return array(
            ["", "", Response::HTTP_NOT_FOUND],
            ["p_carole_test", "", Response::HTTP_NOT_FOUND],
            ["p_carole", "", Response::HTTP_NOT_FOUND],
            ["p_carole", "p4carole", Response::HTTP_OK],
            ["p_carole", "P4CAROLE", Response::HTTP_OK],
        );
    }

    /**
     * @dataProvider loginCheckDataProvider
     */
    public function testLoginCheck($login, $password, $statusCode)
    {
        $client    = static::createClient();
        $client->request('POST', '/login-check', array("login" => $login, "password" => $password));
        $response  = $client->getResponse();
        /** @var User $user */
        $user = $client->getContainer()->get('api.mediator.sdk')->getSdk("user")->getByUsername($login);
        $data      = json_decode($response->getContent(), true);

        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'));
        $this->assertEquals($statusCode, $client->getResponse()->getStatusCode());
        if ($statusCode == Response::HTTP_OK) {
            $this->assertEquals($user->getId(),$data["id"]);
            $this->assertEquals($user->getLastname(),$data["lastname"]);
            $this->assertEquals($user->getFirstname(),$data["firstname"]);
            $this->assertEquals($user->getEmail(),$data["email"]);
            $this->assertEquals($user->getCompany(),$data["company"]);
            $this->assertEquals($user->getRoles(),$data["roles"]);
        }

    }

}

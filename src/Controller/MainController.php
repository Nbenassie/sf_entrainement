<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\TypeAbsence;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;


class MainController extends AbstractController
{
    #[Route('/', name: 'app_main')]
    public function index(HttpClientInterface $client, EntityManagerInterface $em): Response
    {
//        $typeAbsences = $em->getRepository(TypeAbsence::class)->findAll();

        $typeAbsences = $em->getRepository(TypeAbsence::class)->findAll();

        dump($typeAbsences);

        return $this->render('main/index.html.twig', [
            'controller_name' => 'MainController',
            'TypeAbsences' => $typeAbsences

        ]);
    }

    #[Route('/refresh-type-absence', name: 'refresh_type_absence', methods: ['POST'])]
    public function refreshTypeAbsence(HttpClientInterface $client, EntityManagerInterface $em)
    {



        $taboption = [
            'user_login' => 'yanncb',
            'user_password' => '44741c6c9a3dad833026dc3b8a62e38a8341ca52',
            'tiers_id' => '15907',
            'app_version_code' => '57',
            'last_upd' => '0001-00-00 00:00:00'
        ];

        $response = $client->request('POST', 'http://localhost/admin/appli/updateTypeAbsence', [
            'body' => $taboption,
            'headers' => [
                'Content-Type' => 'multipart/form-data'
            ]
        ]);

        if ($response->getStatusCode() == 200) {

            $content = $response->getContent();
            $data = json_decode($content, true);
            if ($data && isset($data['objects'])) {


                $typeAbsences = $em->getRepository(TypeAbsence::class)->findAll();
                foreach ($typeAbsences as $typeAbsence) {
                    $em->remove($typeAbsence);
                }
                $em->flush();

                foreach ($data['objects'] as $object) {

                    $type_absence = new TypeAbsence();
                    $type_absence->setCodetypeAbsence($object['type_absence_id']);
                    $type_absence->setCodeTypeAbsence($object['code_type_absence']);
                    $type_absence->setDenomination($object['name']);
                    $type_absence->setActive($object['is_active']);

                    $em->persist($type_absence);
                    $em->flush();

                }
            }
            $resultupdate = 'mise a jour reussie';
        }
        else {
            $resultupdate = 'mise a jour échouée';
        }

        return $this->render('main/retourUpdate.html.twig', [
            'resultupdate' => $resultupdate,

        ]);
    }
}
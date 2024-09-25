<?php

namespace App\Controller;

use App\Form\MainFormType;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\TypeAbsence;
use mysql_xdevapi\Result;
use PhpParser\Node\Stmt\If_;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;


class MainController extends AbstractController
{
    public HttpClientInterface $client;
    public EntityManagerInterface $em;
    public function __construct(HttpClientInterface $client, EntityManagerInterface $em)
    {
        $this->client = $client;
        $this->em = $em;
    }
/** Main function  */
    #[Route('/', name: 'app_main')]
    public function index(): Response
    {
        $typeAbsences = $this->em->getRepository(TypeAbsence::class)->findAll();
        return $this->render('main/index.html.twig', [
            'controller_name' => 'MainController',
            'TypeAbsences' => $typeAbsences
        ]);
    }
    /** fonction d'actualisation de la page principale */
    #[Route('/refresh-type-absence', name: 'refresh_type_absence')]
    public function refreshTypeAbsence()
    {
        $taboption = [
            'user_login' => 'yanncb',
            'user_password' => '44741c6c9a3dad833026dc3b8a62e38a8341ca52',
            'tiers_id' => '15907',
            'app_version_code' => '57',
            'last_upd' => '0001-00-00 00:00:00'
        ];
        $response = $this->client->request('POST', 'http://localhost/admin/appli/updateTypeAbsence', [
            'body' => $taboption,
            'headers' => [
                'Content-Type' => 'multipart/form-data'
            ]
        ]);

        if ($response->getStatusCode() == 200) {
            $content = $response->getContent();
            $data = json_decode($content, true);

            if ($data && isset($data['objects'])) {
                $typeAbsences = $this->em->getRepository(TypeAbsence::class)->findAll();

                foreach ($typeAbsences as $typeAbsence) {
                    $this->em->remove($typeAbsence);
                }

                $this->em->flush();

                foreach ($data['objects'] as $object) {
                    $type_absence = new TypeAbsence();
                    $type_absence->setCodeTypeAbsence($object['code_type_absence']);
                    $type_absence->setDenomination($object['name']);
                    $type_absence->setActive($object['is_active']);
                    $type_absence->setAbsenceColor($object['color_absence']);
                    $this->em->persist($type_absence);
                    $this->em->flush();
                }
            }
            $resultupdate = 'mise a jour reussie';
        } else {
            $resultupdate = 'mise a jour échouée';
        }
        return $this->redirect($this->generateUrl('app_main'));
    }
    /** Fonction pour supprimer un type absence */
    #[Route('/delete-type-absence', name: 'delete_type_absence')]
    public function deleteTypeAbsence()
    {
        if (isset($_POST['deleteId']) && $_POST['deleteId'] > 0) {
            $id_type_absence = $_POST['deleteId'];
            $taboption = [
                'user_login' => 'yanncb',
                'user_password' => '44741c6c9a3dad833026dc3b8a62e38a8341ca52',
                'tiers_id' => '15907',
                'app_version_code' => '57',
                'code_type_absence' => $id_type_absence
            ];
            $response = $this->client->request('POST', 'http://localhost/admin/appli/deleteTypeAbsence', [
                'body' => $taboption,
                'headers' => [
                    'Content-Type' => 'multipart/form-data'
                ]
            ]);
        }
        $content = $response->getContent();
        return $this->redirect($this->generateUrl('app_main'));
    }
    /** Fonction de modification de type absence */
    #[Route('/edit-type-absence/{id}', name: 'edit_type_absence')]
    public function editTypeAbsence(Request $request, int $id)
    {
      $return = null;
      $type_absence = $this->em->getRepository(TypeAbsence::class)->find($id);
      $form = $this->createForm(MainFormType::class, $type_absence);
      $form->handleRequest($request);
      if ($form->isSubmitted() && $form->isValid()) {
            /** @var TypeAbsence $result */
            $typeAbsence = $form->getData();
            $reussite = $this->webservicemodif($typeAbsence);
            if ($reussite) {
                $this->em->persist($type_absence);
                $this->em->flush();
                $return = $this->redirect($this->generateUrl('app_main'));
            } else {
                $this->addFlash('error', 'Erreur lors de la modification du type absence.');
                $response = new Response();
                $response->setStatusCode(500);
                $return = $this->render('main/edit_type_absence.html.twig', [
                    'form' => $form],
                    $response
                );
            }
        } else {
          $return = $this->render('main/edit_type_absence.html.twig', [
              'form' => $form,
          ]);
      }
      return $return;
    }

    private function webServiceModif(TypeAbsence $typeAbsence):bool {
        $taboption = [
            'user_login' => 'yanncb',
            'user_password' => '44741c6c9a3dad833026dc3b8a62e38a8341ca52',
            'tiers_id' => '15907',
            'app_version_code' => '57',
            'code_type_absence' => $typeAbsence->getCodetypeAbsence(),
            'color_absence' => $typeAbsence->getAbsenceColor()
        ];
        $response = $this->client->request('POST', 'http://localhost/admin/appli/editTypeAbsence', [
            'body' => $taboption,
            'headers' => [
                'Content-Type' => 'multipart/form-data'
            ]
        ]);
        if ($response->getStatusCode() == 200){
            return true;
        } else {
            return false;
        }
    }
}
 


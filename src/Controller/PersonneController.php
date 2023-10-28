<?php

namespace App\Controller;

use DateTime;
use App\Entity\Emplois;
use App\Entity\Personnes;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PersonneController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
  
 #[Route('/personne', name: 'app_personne',methods:"POST")]

public function createPersonne(Request $request): Response
{
    $data = json_decode($request->getContent(), true);

    $personne = new Personnes();
    $personne->setNom($data['nom']);
    $personne->setPrenom($data['prenom']);
    $personne->setDateNaissance(new \DateTime($data['dateNaissance']));
    $dt = new DateTime("Now");
    $interval = $dt->diff(new \DateTime($data['dateNaissance']));
    $age = $interval->y;
    if ($age >= 150) {
        return new JsonResponse(['error' => 'L\'âge de la personne doit être inférieur à 150 ans'], Response::HTTP_BAD_REQUEST);
    }

    $this->entityManager->persist($personne);
    $this->entityManager->flush();

    return new JsonResponse(['id' => $personne->getId(), 'message' => 'Personne créée avec succès'], Response::HTTP_CREATED);
}


#[Route('/emplois/{id}', name: 'app_emplois',methods:"POST")]

public function createEmplois(Request $request, int $id): Response
{
    $data = json_decode($request->getContent(), true);

    $emplois = new Emplois();
    $emplois->setNomEntreprise($data['nomEntreprise']);
    $emplois->setPosteOccupe($data['posteOccupe']);
    $emplois->setDateDebut(new \DateTime($data['dateDebut']));

    if(isset($data['dateFin'])){
        $emplois->setDateFin(new \DateTime($data['dateFin']));
       
    }
    $personneRepository = $this->entityManager->getRepository(Personnes::class);
    $personne =  $personneRepository->findOneBy(['id' => $id]);
    $emplois->addPersonne($personne);

    $this->entityManager->persist($emplois);
    $this->entityManager->flush();

    return new JsonResponse(['id' => $personne->getId(), 'message' => 'Emplois créée avec succès'], Response::HTTP_CREATED);
}

#[Route('/jobslist', name: 'app_jobslist',methods:"GET")]

public function jobslist(Request $request): Response
{
    $personneList = $this->entityManager->getRepository(Personnes::class)->findFields();
   
    $responseArray = [];
   foreach ($personneList as $key => $value){
    
    $jobRepository = $this->entityManager->getRepository(Emplois::class);
    $jobList = $jobRepository->findBypersonField($value["id"]);
     $responseArray[$key]['nom']=$value["nom"];
     $responseArray[$key]['prenom']=$value["prenom"];
     $dt = new DateTime("Now");
    $interval = $dt->diff($value["dateNaissance"]);
    $age = $interval->y;
    $responseArray[$key]['age']=$age;
    foreach($jobList as $job){
       
     $responseArray[$key]['emplois']=$job["posteOccupe"];   
    
    }

    
}
return new Response(json_encode($responseArray));
}

#[Route('/person/{id}', name: 'app_findbyjob',methods:"GET")]

public function findPerson(Request $request,$id): Response
{
    $emplois = $this->entityManager->getRepository(Personnes::class)->findPersonsforjob($id);
  
    return new Response(json_encode( $emplois));
}

#[Route('/jobbydate', name: 'app_jobbydate',methods:"POST")]

public function jobBydate(Request $request): Response
{
    $data = json_decode($request->getContent(), true);
    $emplois = $this->entityManager->getRepository(Emplois::class)->findPersonsforjob($data["id"],new \DateTime($data["date1"]),new \DateTime($data["date2"]));
     
    return new Response(json_encode( $emplois));
}
}
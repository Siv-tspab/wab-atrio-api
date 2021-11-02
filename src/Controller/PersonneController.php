<?php

namespace App\Controller;

use App\Entity\Personne;
use App\Form\PersonneType;
use App\Service\AgeService;
use App\Repository\PersonneRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\View;
use Symfony\Component\BrowserKit\Request;
use Symfony\Component\HttpFoundation\Response;

class PersonneController extends AbstractFOSRestController
{
    public function __construct(private PersonneRepository $repository, private EntityManagerInterface $em, private AgeService $ageService)
    {
    }

    /**
     * @Get(
     *      path = "/personnes",
     *      name = "personnes_get_all",
     * )
     * @View
     */
    public function getAll()
    {
        $personnes = $this->repository->findAllOrderedByName();
        foreach ($personnes as $personne) {
            $personne->setAge($this->ageService->getAgeFromDate($personne->getBirthday()) . " ans");
        }
        return $personnes;
    }

    /**
     * @Post(
     *      path = "/personnes",
     *      name = "personnes_create",
     * )
     * @View
     */
    public function post(Request $request)
    {
        $data = $this->serializer->deserialize($request->getContent(), 'array', 'json');
        $form = $this->createForm(PersonneType::class, new Personne);
        $form->submit($data);

        if ($form->isSubmitted() && $form->isValid()) {

            $personne = $form->getData();

            $this->em->persist($personne);
            $this->em->flush();

            return $this->view($personne, Response::HTTP_CREATED);
        }
        return new Response('', Response::HTTP_BAD_REQUEST);
    }
}

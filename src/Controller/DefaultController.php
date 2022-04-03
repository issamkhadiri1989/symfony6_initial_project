<?php

declare(strict_types=1);

namespace App\Controller;

use App\Form\Type\ExampleType;
use http\Message\Body;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormErrorIterator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    private FormFactoryInterface $factory;

    public function __construct(FormFactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    #[Route('/', name: 'app_default')]
    public function index(): Response
    {
        $form = $this->factory->create(ExampleType::class);

        return $this->render('default/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/process', name: 'app_process_default')]
    public function ajax(Request $request): JsonResponse
    {
        $form = $this->factory->create(ExampleType::class);
        $form->handleRequest($request);
        $errors = [];
        $status = Response::HTTP_OK;
        if ($request->isXmlHttpRequest() === true && $form->isSubmitted() === true) {
            if ($form->isValid() === true) {
                $data = $form->getData();
                // do something with $data like persist to database
            } else {
                $errors['global'] = (string) $form->getErrors(false); // global errors.
                $errors['message'] = (string) $form['message']->getErrors(false); // field specific errors.
                $status = Response::HTTP_UNPROCESSABLE_ENTITY;
            }
        }

        return new JsonResponse($errors, $status);
    }
}

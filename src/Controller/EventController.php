<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Event;
use App\Form\UserType;
use App\Repository\EventRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;

class EventController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function home()
    {
        return $this->render('event/home.html.twig', [
            'controller_name' => 'EventController',
        ]);
    }

    /**
     * @Route("/events", name="events")
     */
    public function events(EventRepository $repository)
    {
        $events = $repository->findAll();
        return $this->render('event/events.html.twig', [
            'events' => $events,
        ]);
    }

    /**
     * @Route("event/new", name="event_create")
     * @Route("/event/{id}/edit", name="event_edit")
     */
    public function form(Event $event = null, Request $request, EntityManagerInterface $entityManager)
    {
        if (!$event) {
            $event = new Event();
        }


        $form = $this->createFormBuilder($event)
            ->add('title')
            ->add('description')
            ->add('dateEvent')
            ->add('timeStart')
            ->add('timeEnd')
            ->add('place')
            ->add('numberMax')
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($event);
            $entityManager->flush();
        }

        return $this->render('event/create_event.html.twig', [
            'formEvent' => $form->createview()
        ]);
    }

    /**
     * @Route("/event/{id}", name="event")
     */
    public function event(Event $event, EntityManagerInterface $entityManager, Request $request)
    {
        $user = new User();
        $user->setEvent($event);
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'Bravo ! tu es inscrit(e) à l\'évènement'
            );

            return $this->redirectToRoute('events');
        }

        return $this->render('event/event.html.twig', [
            'formUser' => $form->createview(),
            'event' => $event,
            
        ]);
    }
}

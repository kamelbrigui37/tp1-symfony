<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class MailerController extends AbstractController
{
    #[Route('/send-email', name: 'app_send_email')]
    public function sendEmail(MailerInterface $mailer): Response
    {
        $email = (new Email())
            ->from('kamelbrigui37@gmail.com')
            ->to('briguik4@gmail.com')
            ->subject('Nouvel Article !')
            ->text('Un nouvel article a été publié sur le blog.')
            ->html('<p>Un nouvel article a été publié sur le blog.</p>');

        $mailer->send($email);

        return new Response('Email envoyé avec succès !');
    }
}

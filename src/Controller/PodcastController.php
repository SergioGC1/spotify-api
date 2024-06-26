<?php

namespace App\Controller;

use App\Entity\Podcast;
use App\Entity\Usuario;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;

class PodcastController extends AbstractController
{
    public function podcasts(Request $request, SerializerInterface $serializer)
    {
        if($request->isMethod('GET')) {
            $podcasts = $this->getDoctrine()->getRepository(Podcast::class)->findAll();
            $podcasts = $serializer->serialize($podcasts, 'json', ['groups' => 'podcast']);

            return new Response($podcasts);
        }
    }

    public function podcast(Request $request, SerializerInterface $serializer)
    {
        $id = $request->get('id');

        if($request->isMethod('GET')) {
            $podcast = $this->getDoctrine()->getRepository(Podcast::class)->findOneBy(['id' => $id]);
            $podcast = $serializer->serialize($podcast, 'json', ['groups' => 'podcast']);

            return new Response($podcast);
        }
    }

    public function podcastsUsuario(Request $request, SerializerInterface $serializer)
    {
        $id = $request->get('id');

        if($request->isMethod('GET')) {
           $usuario = $this->getDoctrine()->getRepository(Usuario::class)->findOneBy(['id' => $id]);
           $podcasts = $usuario->getPodcast();
           $podcasts = $serializer->serialize($podcasts, 'json', ['groups' => 'podcast']);

           return new Response($podcasts);
        }
    }

    public function seguirPodcast(Request $request, SerializerInterface $serializer)
    {
        $idUsuario = $request->get('usuarioId');
        $idPodcast = $request->get('podcastId');

        $usuario = $this->getDoctrine()->getRepository(Usuario::class)->findOneBy(['id' => $idUsuario]);
        $podcast = $this->getDoctrine()->getRepository(Podcast::class)->findOneBy(['id' => $idPodcast]);

        if(!$podcast){
            return new Response('Podcast no existente');
            
        }else{
            
            if($request->isMethod('POST')) {

                if($usuario->getPodcast()->contains($podcast)){
                    return new Response('Ya sigues a este podcast'); 
                }

                $usuario->getPodcast()->add($podcast);
                $this->getDoctrine()->getManager()->persist($usuario);
                $this->getDoctrine()->getManager()->flush();
                $podcast = $serializer->serialize($podcast, 'json', ['groups' => 'podcast']);
                
                return new Response($podcast);
            }
            
            if($request->isMethod('DELETE')) {
                
                if(!$usuario->getPodcast()->contains($podcast)){
                    return new Response('No sigues a este podcast'); 
                }

                $usuario->getPodcast()->removeElement($podcast);
                $this->getDoctrine()->getManager()->persist($usuario);
                $this->getDoctrine()->getManager()->flush();
                $podcast = $serializer->serialize($podcast, 'json', ['groups' => 'podcast']);

                return new Response($podcast);
            }
        }
    }
}

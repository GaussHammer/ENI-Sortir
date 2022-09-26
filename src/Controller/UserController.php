<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\MyProfileType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use \Symfony\Component\HttpFoundation\Request ;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class UserController extends AbstractController
{
    /**
     * @Route("/myProfile", name="app_user")
     */
    public function MyProfile(): Response
    {
        return $this->render('user/MyProfile.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }
    /**
     * @Route ("/myProfile/modify",name="modify_profile")
     */
    public function ProfileModifier(Request $request,
    EntityManagerInterface $entityManager): Response{
        $user = new User();
        $userForm = $this->createForm(MyProfileType::class, $user, array('method'=>'UPDATE'));
        $userForm->handleRequest($request);

        if($userForm->isSubmitted()){
            $user->setPseudo($userForm->get('pseudo')->getData());
            $entityManager->flush($user);
        }

        return $this->render('user/ProfileModifier.html.twig',[
        'userForm' => $userForm->createView()
        ]);
    }
    /**
     * @Route("/profile/{id}", name="user_profile")
     */
    public function UserProfile(int $id, UserRepository $userRepository): Response
    {
        $user = $userRepository->find($id);
        return $this->render('user/UserProfile.html.twig', [
            "user" => $user
        ]);
    }
}

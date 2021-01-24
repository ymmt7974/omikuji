<?php
namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use AppBundle\Entity\Unsei;
use AppBundle\Form\UnseiType;
use Doctrine\ORM\EntityManager;

/**
 * @Route("/unsei")
 */
class UnseiController extends Controller
{
    /**
     * 運勢一覧の表示
     * 
     * @Route("/", name="unsei_index")
     * @Method("GET")  // ①
     * 
     * @param Request $request
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $repository = $this->getDoctrine()->getRepository(Unsei::class);
        $unseis = $repository->findAll();

        return $this->render('unsei/index.html.twig', ['unseis' => $unseis]);
    }
    
    /**
     * 運勢の新規作成
     * 
     * @Route("/new", name="unsei_new")
     * @Method({"GET", "POST"}) 
     * 
     * @param Request $request
     * @return Response
     */ 
    public function newAction(Request $request)
    {
        $unsei = new Unsei();
        
        // $form = $this->createFormBuilder($unsei)
        //     ->add('name', TextType::class)
        //     ->getForm();
        $form = $this->createForm(UnseiType::class, $unsei); // ①

        // Form送信のハンドリング
        $form->handleRequest($request);                   // ①
        if ($form->isSubmitted() && $form->isValid()) {   // ②
            /** @var EntityManager $em */
            $em = $this->getDoctrine()->getManager();     // ③
            $em->persist($unsei);
            $em->flush();
            
            return $this->redirectToRoute('unsei_index'); // ④
        }

        return $this->render('unsei/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    
    /**
     * 運勢の編集
     * 
     * @Route("/{id}/edit", name="unsei_edit")
     * @Method({"GET", "PUT"})  // (a)
     * 
     * @param Request $request
     * @return Response
     */
    public function editAction(Request $request, Post $post) // タイプヒントで"Post"を明記
    {
        // Do something
        $repository = $this->getDoctrine()->getRepository(Unsei::class);
        $unsei = $repository->find($id);

        if (!$unsei) {
            throw $this->createNotFoundException('No unsei found for id '.$id);
        }
        
        $form = $this->createForm(UnseiType::class, $unsei, [ // ①
            'method' => 'PUT',  // ②
        ]);
        
        $form->handleRequest($request); // ③
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush(); // ④
            
            return $this->redirectToRoute('unsei_index');
        }

        return $this->render('unsei/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    
    /**
     * 運勢の削除
     * 
     * @Route("/{id}", name="unsei_delete")
     * @Method("DELETE") // ①
     * 
     * @param Request $request
     * @return Response
     */
    public function deleteAction(Request $request, $id)
    {
        $repository = $this->getDoctrine()->getRepository(Unsei::class);
        $unsei = $repository->find($id);

        if (!$unsei) {
            throw $this->createNotFoundException('No unsei found for id '.$id);
        }
        
        if ($this->isCsrfTokenValid('unsei', $request->get('_token'))) { // ②
            $em = $this->getDoctrine()->getManager();
            $em->remove($unsei); // ③
            $em->flush();
        }

        return $this->redirectToRoute('unsei_index');
    }



    /**
     * @Route("/validate/{name}", defaults={"name" = ""})
     */
    public function validateAction($name)
    {
        $unsei = new Unsei();
        $unsei->setName($name);

        $validator = $this->get('validator');
        $errors = $validator->validate($unsei);

        if (count($errors) > 0) {
            return $this->render('unsei/validate.html.twig', [ 
                'errors' => $errors,
            ]);
        }

        return new Response("「{$name}」は正しい運勢です！");
    }
}
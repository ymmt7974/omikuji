<?php
namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Entity\Unsei;

/**
 * @Route("/unsei")
 */
class UnseiController extends Controller
{
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
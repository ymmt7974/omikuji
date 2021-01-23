<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;   // (a)
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route; // (b)
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class OmikujiController extends Controller  // ①
{
    /**
     * おみくじ運勢を表示する
     *
     * // ①
     * @Route("/omikuji/{yourname}", defaults={"yourname" = "YOU"}, name="omikuji")
     * 
     * @param Request $request
     * @return Response
     */
    public function omikujiAction(Request $request, $yourname)  // ②
    {
        $omikuji = ['大吉', '中吉', '小吉', '末吉', '凶'];
        $number = rand(0, count($omikuji) - 1);

        // return new Response(  // ③
        //     "<html><body>{$yourname}さんの運勢は $omikuji[$number] です。</body></html>"
        // );
        return $this->render('omikuji/omikuji.html.twig', [ // ①
            'name' => $yourname,
            'unsei' => $omikuji[$number],
        ]);
    }
}

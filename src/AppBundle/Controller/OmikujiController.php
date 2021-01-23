<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;   // (a)
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route; // (b)
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Entity\Unsei;                // (a)
use AppBundle\Repository\UnseiRepository;  // (b)

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
        // $omikuji = ['大吉', '中吉', '小吉', '末吉', '凶'];
        $repository = $this->getDoctrine()->getRepository(Unsei::class);
        $omikuji = $repository->findAll();

        $number = rand(0, count($omikuji) - 1);

        // return new Response(  // ③
        //     "<html><body>{$yourname}さんの運勢は $omikuji[$number] です。</body></html>"
        // );
        return $this->render('omikuji/omikuji.html.twig', [ // ①
            'name' => $yourname,
            'unsei' => $omikuji[$number],
        ]);
    }

    /**
     * @Route("/find")
     */
    public function findAction()
    {
        /**
         * エンティティの検索はリポジトリを通して行う ①
         * @var UnseiRepository $repository 
         */
        $repository = $this->getDoctrine()->getRepository(Unsei::class);

        // 全て検索 ②
        $unseis = $repository->findAll();
        dump($unseis);
        
        // ID(プライマリキー)で検索 ③
        $unsei = $repository->find(1);
        dump($unsei);

        // 複数の項目で１件だけ検索（ここでは'name'だけですが...）④
        $unsei = $repository->findOneBy([
            'name' => '大吉',
        ]);
        dump($unsei);
        
        // 複数の項目で複数件検索（ここでは'name'だけですが...）⑤
        // ※ 配列が返ってくる
        $unsei = $repository->findBy([
            'name' => '大吉',
        ]);
        dump($unsei);
        
        // プロパティに対応したダイナミックメソッドを使って１件だけ検索 ⑥
        $unsei = $repository->findOneById(1);
        dump($unsei);
        $unsei = $repository->findOneByName('中吉');
        dump($unsei);
        
        // ダイナミックメソッドを使って複数件検索 ⑦
        $unsei = $repository->findByName('中吉');
        dump($unsei);
        
        die; // プログラムを終了して、dumpを画面に表示 ⑧
        return new Response("Dummy");
    }

    /**
     * @Route("/crud")
     */
    public function crudAction()
    {
        /**
         * エンティティの作成、更新、削除はエンティティマネージャを通して行う ①
         *  @var EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        //
        // Create
        //
        $unsei = new Unsei();    // ②
        $unsei->setName("大凶");
        dump($unsei);

        $em->persist($unsei);    // ③
        $em->flush();            // ④
        dump($unsei);

        //
        // Read ⑤
        //
        $repository = $em->getRepository(Unsei::class);
        
        /** @var Unsei $unsei */
        $unsei = $repository->findOneByName('大凶'); // ⑥
        dump($unsei);
        
        //
        // Update ⑦
        //
        $unsei->setName("大大吉");
        $em->flush();
        dump($unsei);
        
        $unsei = $repository->find($unsei->getId());
        dump($unsei);
        
        // 
        // Delete ⑧
        // 
        $em->remove($unsei);
        $em->flush();
        
        $unseis = $repository->findAll();
        dump($unseis);
        foreach ($unseis as $unsei) {
            dump($unsei->getName());
        }
        
        die; // プログラムを終了して、dumpを画面に表示

        return new Response("Dummy");
    }

    /**
     * @Route("/dql")
     */
    public function dql()
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
            'SELECT u
            FROM AppBundle:Unsei u
            WHERE u.name = :name'        // ①
        )->setParameter('name', '大吉');
        
        $unsei = $query->getResult();
        dump($unsei);
        
        die; // プログラムを終了して、dumpを画面に表示

        return new Response("Dummy");
    }

    /**
     * @Route("/qb")
     */
    public function queryBuilder()
    {
        /** @var UnseiRepository $repository **/
        $repository = $this->getDoctrine()->getRepository(Unsei::class);
        
        $query = $repository->createQueryBuilder('u')
            ->where('u.name = :name')
            ->setParameter('name', '大吉')
            ->getQuery();

        $unsei = $query->getResult();
        dump($unsei);
        
        die; // プログラムを終了して、dumpを画面に表示

        return new Response("Dummy");
    }
}

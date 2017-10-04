<?php
/**
 *
 * Simon Brown <uptoeleven@gmail.com>
 */

namespace AppBundle\Controller;

use AppBundle\Form\BoatForm;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Service\CalculateRequirement;
use AppBundle\Entity\Boat;

/**
 * Class FrontEndController
 * @package AppBundle\Controller
 */
class BoatController extends Controller
{
    /**
     * @Route("/", name="fishing_boat")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function boatAction(Request $request)
    {
        $form = $this->createForm(BoatForm::class);

        $form->handleRequest($request);
        $hpRequirement = '';
        if ($form->isSubmitted() && $form->isValid()) {
            $boat = $form->getData();
            $calculator = $this->get(CalculateRequirement::class)->setBoat($boat);
            $hpRequirement = $calculator->generateSpeedPowerCurve();
        }

        return $this->render('boat-view/boat.html.twig', [
            'boatForm'     => $form->createView(),
            'hpRequirement' => $hpRequirement
        ]);
    }
}
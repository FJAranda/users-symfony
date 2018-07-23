<?php

namespace UsersBundle\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use UsersBundle\Entity\Euroformac;
use UsersBundle\Form\EuroformacType;
use Symfony\Component\HttpFoundation\Session\Session;

class DefaultController extends Controller
{
    
    private $session;
    
    public function __construct() {
        $this->session = new Session();
    }
    
    public function indexAction(Request $request)
    {   
        //Inicializacion del objeto y el formulario
        $euroformac = new Euroformac();
        $form = $this->createForm(EuroformacType::class,$euroformac);
        
        $form->handleRequest($request);
        //Comprobacion formulario ha sido enviado para mostrar mensajes
        if ($form->isSubmitted()) {
            
            if ($form->isValid()) {
                //Cargo los datos del formulario al objeto 
                $euroformac->setNombre($form->get("nombre")->getData());
                $euroformac->setEmail($form->get("email")->getData());
                $euroformac->setTelefono($form->get("telefono")->getData());

                //Obtengo el manager de doctrine y guardo el objeto en la base de datos
                $manager = $this->getDoctrine()->getManager();
                $manager->persist($euroformac);

                $flush = $manager->flush();

                if ($flush == null) {
                    $status = "Añadido correctamente a la base de datos";
                }else{
                    $status = "Error al añadir a la base de datos";
                }
            }else{
                $status = "Fallo al validar el formulario";
            }
            
            //Añado el resultado de la operacion a la lista de mensajes de session
            $this->session->getFlashBag()->add("status", $status);
        }
        return $this->render('@Users/Default/index.html.twig', array(
            'form' => $form->createView(),
        ));
    }
}

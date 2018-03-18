<?php
namespace UserEx\MyDrugstore\Controllers;

use UserEx\MyDrugstore\Core\Controller;
use Nette\Http\Request;
use UserEx\MyDrugstore\Core\Response;

class HelloWorldController extends Controller
{
    /**
     * @param Request $request
     * 
     * @return \UserEx\MyDrugstore\Core\Response
     */
    public function indexAction (Request $request)
    {           
        return new Response($this->view('hello_world.html.twig', array('msg' => 'Hello, world!!!')));
    }
}
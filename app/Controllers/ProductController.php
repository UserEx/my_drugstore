<?php
namespace UserEx\MyDrugstore\Controllers;

use UserEx\MyDrugstore\Core\Controller;
use Nette\Http\Request;
use UserEx\MyDrugstore\Core\Response;
use UserEx\MyDrugstore\Entities\Product;
use UserEx\MyDrugstore\Core\RedirectResponse;
use UserEx\MyDrugstore\Core\Exceptions\NotFoundException;

class ProductController extends Controller
{
    /**
     * @param Request $request
     *
     * @return \UserEx\MyDrugstore\Core\Response
     */
    public function listAction (Request $request)
    {
        return new Response($this->view('product_list.html.twig'));
    }
    
    /**
     * @param Request $request
     *
     * @return \UserEx\MyDrugstore\Core\Response
     */
    public function newAction(Request $request)
    {
        return new Response($this->view('product_new.html.twig', array()));
    }
    
    /**
     * @param Request $request
     *
     * @return \UserEx\MyDrugstore\Core\Response
     */
    public function addAction(Request $request)
    {
        $product = new Product();
        $invalidFeedback = array();
        
        if ($name = $request->getPost('name')) {
            $product->setName($name);
        } else {
            $invalidFeedback['name'] = 'Укажите название продукта';
        }
        
        if ($price = $request->getPost('price')) {
            if (is_numeric($price)) {
                $product->setPrice($price);
            } else {
                $invalidFeedback['price'] = 'Укажите корректную цену продукта' . $price;
            }
            
        } else {
            $invalidFeedback['price'] = 'Укажите цену продукта';
        }
        
        if ($invalidFeedback) {
            error_log('debud: addAction and invalid_feedback!', E_USER_NOTICE);
            $response = new Response($this->view('product_new.html.twig', array('invalid_feedback' => $invalidFeedback)));
        } else {
            $product->setDescription($request->getPost('description'));
            $product->setProducingCountry($request->getPost('producing_country'));
            $product->setManufacturer($request->getPost('manufacturer'));
            
            if ($date = $request->getPost('expiry_date')) {
                $product->setExpiryDate(\DateTime::createFromFormat('Y-m-d', $date));
            }
            
            $router = $this->container['router'];
            $em = $this->container['em'];
            
            $em->getRepository('UserEx\MyDrugstore\Entities\Product')
                ->add($product);
            
            $response = new RedirectResponse($router->getUrl('product_view') . '?id=' . $product->getId());
        }
        
        return $response;        
    }
    
    /**
     * @param Request $request
     *
     * @return \UserEx\MyDrugstore\Core\Response
     */
    public function viewAction(Request $request)
    {
        $id = $request->getQuery('id');
        
        if ($id) {
            $em = $this->container['em'];
            
            $product = $em->getRepository('UserEx\MyDrugstore\Entities\Product')
                ->find($id);
        }
        
        if (!$id || !$product) {
            throw new NotFoundException();
        }
        
        return new Response($this->view('product_view.html.twig', array('product' => $product)));
    }
}
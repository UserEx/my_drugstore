<?php
namespace UserEx\MyDrugstore\Controllers;

use UserEx\MyDrugstore\Core\Controller;
use Nette\Http\Request;
use UserEx\MyDrugstore\Core\Response;
use UserEx\MyDrugstore\Entities\Product;
use UserEx\MyDrugstore\Core\RedirectResponse;
use UserEx\MyDrugstore\Core\Exceptions\NotFoundException;
use UserEx\MyDrugstore\Repositories\ProductRepository;
use Doctrine\ORM\Query\ResultSetMapping;

class ProductController extends Controller
{
    /**
     * @param Request $request
     *
     * @return \UserEx\MyDrugstore\Core\Response
     */
    public function listAction (Request $request)
    {
        $em = $this->container['em'];
        
        /* @var $repository ProductRepository */
        $repository = $em->getRepository('UserEx\MyDrugstore\Entities\Product');
        
        if ($search = $request->getQuery('search')) {
            
            $keywordsArray = explode(' ', $search);
            foreach ($keywordsArray as $index => $keyword) {
                $keywordsArray[$index] = '' . $keyword . '';
            }
            $keywords = implode('', $keywordsArray);
            
            
            $rsm = new ResultSetMapping();
            $rsm->addEntityResult('UserEx\MyDrugstore\Entities\Product', 'p');
            $rsm->addFieldResult('p', 'id', 'id');
            $rsm->addFieldResult('p', 'product_name', 'name');
            $rsm->addFieldResult('p', 'producing_country', 'producingCountry');
            $rsm->addFieldResult('p', 'manufacturer', 'manufacturer');
            $rsm->addFieldResult('p', 'description', 'description');            
            $rsm->addFieldResult('p', 'price', 'price');
            
            
            $query = $em->createNativeQuery('SELECT id, product_name, description, manufacturer, producing_country, expiry_date FROM product WHERE MATCH(product_name, description) AGAINST( ? IN BOOLEAN MODE)', $rsm);
            $query->setParameter(1, '\'+'. $keywords .'\'*');
            error_log('debug: listAction! keywords = ' . $keywords, E_USER_NOTICE);
            
            $products = $query->getResult();
            error_log('Debug: count native = ' . count($products), E_USER_NOTICE);    
        } else {
            $products = $repository->findAll();
        }
        
        return new Response($this->view('product_list.html.twig', array('products' => $products)));
    }
    
    /**
     * @param Request $request
     *
     * @return \UserEx\MyDrugstore\Core\Response
     */
    public function newAction(Request $request)
    {
        return new Response($this->view('product_edit.html.twig', array('action' => 'add')));
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
    
    /**
     * @param Request $request
     *
     * @return \UserEx\MyDrugstore\Core\Response
     */
    public function editAction(Request $request)
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
        
        return new Response($this->view('product_edit.html.twig', array('product' => $product, 'action' => 'edit')));
    }
    
    /**
     * @param Request $request
     *
     * @return \UserEx\MyDrugstore\Core\Response
     */
    public function saveAction(Request $request)
    {
        $id = $request->getPost('id');
        $action = $request->getPost('action');
        
        if ($action == 'edit' && $id) {
            $em = $this->container['em'];
            
            $product = $em->getRepository('UserEx\MyDrugstore\Entities\Product')
                ->find($id);
        } elseif ($action == 'add') {
            $product = new Product();
        }
        
        if ($action == 'edit' && !$product) {
            throw new NotFoundException();
        }
       
        $invalidFeedback = array();
        
        $product->setName($request->getPost('name'));
        if (!$product->getName()) {
            $invalidFeedback['name'] = 'Укажите название продукта';
        }
        
        if ($price = $request->getPost('price')) {
            if (!is_numeric($price)) {
                $invalidFeedback['price'] = 'Укажите корректную цену продукта' . $price;
            }
        } else {
            $invalidFeedback['price'] = 'Укажите цену продукта';
        }
        
        $product->setPrice($price);
        $product->setDescription($request->getPost('description'));
        $product->setProducingCountry($request->getPost('producing_country'));
        $product->setManufacturer($request->getPost('manufacturer'));
        
        if ($date = $request->getPost('expiry_date')) {
            $experyDate = \DateTime::createFromFormat('Y-m-d', $date);
        }
        $product->setExpiryDate($experyDate);
        
        if ($invalidFeedback) {
            error_log('debud: addAction and invalid_feedback!', E_USER_NOTICE);
            $response = new Response($this->view('product_edit.html.twig', array('product' => $product, 'invalid_feedback' => $invalidFeedback)));
        } else {            
            $router = $this->container['router'];
            $em = $this->container['em'];
            
            if ($action == 'add') {
                $em->getRepository('UserEx\MyDrugstore\Entities\Product')
                    ->add($product);
            } elseif ($action == 'edit') {
                $em->flush();
            }
            $response = new RedirectResponse($router->getUrl('product_view') . '?id=' . $product->getId());
        }
        
        return $response;
    }
    
    /**
     * @param Request $request
     *
     * @return \UserEx\MyDrugstore\Core\Response
     */
    public function deleteAction(Request $request)
    {
        $id = $request->getQuery('id');
        
        if ($id) {
            $em = $this->container['em'];
            
            $product = $em->getRepository('UserEx\MyDrugstore\Entities\Product')
                ->find($id);
        }
        
        error_log('debug: deleteAction!', E_USER_NOTICE);
        
        if (!$product) {
            throw new NotFoundException();
        }
        
        $em->remove($product);
        $em->flush();
        
        $router = $this->container['router'];
                
        return new RedirectResponse($router->getUrl('hello_world'));
    }
    
    
    
}
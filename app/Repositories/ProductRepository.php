<?php
namespace UserEx\MyDrugstore\Repositories;

use Doctrine\ORM\EntityRepository;
use UserEx\MyDrugstore\Entities\Product;

/**
 * @author ildar
 */
class ProductRepository extends EntityRepository
{
    /**
     * @param Product $todo
     */
    public function add(Product $todo) 
    {
        $this->getEntityManager()->persist($todo);
        $this->getEntityManager()->flush();
    }
    
    /**
     * @param User $user
     * 
     * @return array
     */
    public function getList() 
    {
        return $this->findAll();
    }
    
    /**
     * @param User $user
     */
    public function delete(Product $product)
    {
        $em = $this->getEntityManager();        
        $em->remove($product);
        
        $em->flush();
    }
}
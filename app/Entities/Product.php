<?php
namespace UserEx\MyDrugstore\Entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * Product
 *
 * @ORM\Entity(repositoryClass="UserEx\Todo\Repositories\ProductRepository")
 * @ORM\Table(name="product")
 */
class Product
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=512)
     */
    private $name;
    
    /**
     * @var float
     *
     * @ORM\Column(name="price", type="decimal", scale=2)
     */
    private $price;
    
    /**
     * @var string
     *
     * @ORM\Column(name="producing_country", type="string", length=512, nullable=true)
     */
    private $producingCountry = null;
    
    /**
     * @var string
     *
     * @ORM\Column(name="manufacturer", type="string", length=512, nullable=true)
     */
    private $manufacturer = null;
    
    /**
     * @var \Date
     *
     * @ORM\Column(name="expiry_date", type="date", nullable=true)
     */
    private $expiryDate = null;
    
    
    /**
     * @var string
     * 
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;
    
    
    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }
    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     * 
     * @return Product
     */
    public function setTitle($title)
    {
        $this->title = $title;
        
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     * 
     * @return Product
     */
    public function setUser($user)
    {
        $this->user = $user;
        
        return $this;
    }

    /**
     * @return boolean
     */
    public function isCompleted()
    {
        return $this->completed;
    }

    /**
     * @param boolean $completed
     * 
     * @return Product
     */
    public function setCompleted($completed)
    {
        $this->completed = $completed;
        
        return $this;
    }
    
    /**
     * @return array
     */
    public function toArray()
    {
        return array(
            'id' => $this->getId(), 
            'title' => $this->getTitle(), 
            'completed' => $this->isCompleted()
        );
    }
}

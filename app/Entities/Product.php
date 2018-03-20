<?php
namespace UserEx\MyDrugstore\Entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * Product
 *
 * @ORM\Entity(repositoryClass="UserEx\MyDrugstore\Repositories\ProductRepository")
 * @ORM\Table(name="product", indexes={@ORM\Index(columns={"product_name", "description"}, flags={"fulltext"})})
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
     * @ORM\Column(name="product_name", type="string", length=512)
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
     * @ORM\Column(name="description", type="text", nullable=true)
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
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * 
     * @return Product
     */
    public function setName($name)
    {
        $this->name = $name;
        
        return $this;
    }

    /**
     * @return float
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param float $price
     * 
     * @return Product
     */
    public function setPrice($price)
    {
        $this->price = $price;
        
        return $this;
    }

    /**
     * @return string
     */
    public function getProducingCountry()
    {
        return $this->producingCountry;
    }

    /**
     * @param string $producingCountry
     * 
     * @return Product
     */
    public function setProducingCountry($producingCountry)
    {
        $this->producingCountry = $producingCountry;
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getManufacturer()
    {
        return $this->manufacturer;
    }

    /**
     * @param string $manufacturer
     */
    public function setManufacturer($manufacturer)
    {
        $this->manufacturer = $manufacturer;
    }

    /**
     * @return Date
     */
    public function getExpiryDate()
    {
        return $this->expiryDate;
    }

    /**
     * @param \Date $expiryDate
     * 
     * @return Product
     */
    public function setExpiryDate($expiryDate)
    {
        $this->expiryDate = $expiryDate;
        
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     * 
     * @return Product
     */
    public function setDescription($description)
    {
        $this->description = $description;
        
        return $this;
    }
}

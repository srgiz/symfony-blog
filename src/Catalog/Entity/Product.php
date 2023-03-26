<?php
declare(strict_types=1);

namespace App\Catalog\Entity;

use App\Catalog\Repository\ProductRepository;
use App\Core\Doctrine\Mapping\Trigger;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
#[Trigger('init_product_attribute_value')]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(type: 'bigint')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $name = null;

    #[ORM\OneToOne(mappedBy: 'product', targetEntity: ProductAttributeValue::class, fetch: 'EXTRA_LAZY')]
    private ?ProductAttributeValue $attrValues = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    public function getAttrValues(): ?ProductAttributeValue
    {
        return $this->attrValues;
    }

    public function setAttrValues(ProductAttributeValue $obj): static
    {
        $obj->setProduct($this);
        $this->attrValues = $obj;
        return $this;
    }
}

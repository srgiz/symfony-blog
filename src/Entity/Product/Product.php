<?php
declare(strict_types=1);

namespace App\Entity\Product;

use App\Doctrine\Mapping\Trigger;
use App\Repository\Product\ProductRepository;
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

    #[ORM\OneToOne(mappedBy: 'product', targetEntity: ProductAttributeValue::class)]
    private ?ProductAttributeValue $values = null;

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

    public function getValues(): ?ProductAttributeValue
    {
        return $this->values;
    }

    public function setValues(ProductAttributeValue $values): static
    {
        $values->setProduct($this);
        $this->values = $values;
        return $this;
    }
}

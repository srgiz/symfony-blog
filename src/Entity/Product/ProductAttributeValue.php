<?php
declare(strict_types=1);

namespace App\Entity\Product;

use App\Doctrine\Mapping\Trigger;
use App\Repository\Product\ProductAttributeValueRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductAttributeValueRepository::class)]
#[Trigger('throw_delete_product_attribute_value')]
class ProductAttributeValue
{
    #[ORM\Id]
    #[ORM\Column(type: 'bigint')]
    private ?int $product_id = null;

    #[ORM\Column(type: 'json', nullable: true, options: ['jsonb' => true])]
    private array $values = [];

    #[ORM\OneToOne(inversedBy: 'values', targetEntity: Product::class, cascade: ['all'])]
    #[ORM\JoinColumn(name: 'product_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private ?Product $product = null;

    public function getProductId(): ?int
    {
        return $this->product_id;
    }

    public function setProductId(int $id): static
    {
        $this->product_id = $id;
        return $this;
    }

    public function getValues(): array
    {
        return $this->values;
    }

    public function setValues(array $values): static
    {
        $this->values = $values;
        return $this;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(Product $product): static
    {
        $this->product_id = $product->getId();
        $this->product = $product;
        return $this;
    }
}

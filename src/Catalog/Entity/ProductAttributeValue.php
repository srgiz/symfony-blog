<?php
declare(strict_types=1);

namespace App\Catalog\Entity;

use App\Catalog\Repository\ProductAttributeValueRepository;
use App\Core\Doctrine\Mapping\Trigger;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductAttributeValueRepository::class)]
#[Trigger('throw_delete_product_attribute_value')]
class ProductAttributeValue
{
    #[ORM\Id]
    #[ORM\Column(type: 'bigint')]
    private ?int $product_id = null;

    /** @var array<string, string[]> Значения всегда в строковом виде */
    #[ORM\Column(type: 'json', nullable: true, options: ['jsonb' => true])]
    private array $values = [];

    #[ORM\OneToOne(inversedBy: 'attrValues', targetEntity: Product::class, cascade: ['all'])]
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

    /**
     * @return array<string, string[]>
     */
    public function getValues(): array
    {
        return $this->values;
    }

    /**
     * @param array<string, string[]> $values ['color' => ['red', 'blue'], 'width' => ['2.0']]
     */
    public function setValues(array $values, bool $merge = true): static
    {
        if (!$merge) {
            $this->values = [];
        }

        foreach ($values as $attribute => $attrValues) {
            foreach ($attrValues as $value) {
                $this->values[$attribute][] = (string)$value;
            }

            $this->values[$attribute] = array_values(array_unique($this->values[$attribute], SORT_STRING));
        }

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

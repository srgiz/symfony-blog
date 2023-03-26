<?php
declare(strict_types=1);

namespace App\Core\Dto\Paginate;

class Paginate
{
    private iterable $items = [];

    /** @var array<SortLink> */
    private array $sortLinks = [];

    /** @var array<PageLink> */
    private array $pageLinks = [];

    public function getItems(): iterable
    {
        return $this->items;
    }

    public function setItems(iterable $items): static
    {
        $this->items = $items;
        return $this;
    }

    /**
     * @return array<SortLink>
     */
    public function getSortLinks(): array
    {
        return $this->sortLinks;
    }

    public function addSortLink(SortLink $link): static
    {
        $this->sortLinks[] = $link;
        return $this;
    }

    /**
     * @return array<PageLink>
     */
    public function getPageLinks(): array
    {
        return $this->pageLinks;
    }

    public function addPageLink(PageLink $link): static
    {
        $this->pageLinks[] = $link;
        return $this;
    }
}

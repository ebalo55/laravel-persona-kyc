<?php

namespace Doinc\PersonaKyc\Models;

use ArrayAccess;
use Doinc\PersonaKyc\Exceptions\NoNextPage;
use Doinc\PersonaKyc\Exceptions\NoPreviousPage;
use Illuminate\Support\Arr;

class PaginatedInquiries extends PersonaPagination implements IPersonaModel
{
    use PersonaModel;

    /** @var Inquiry[]  */
    public readonly array $inquiries;

    private function __construct(array $arr)
    {
        parent::__construct($arr);

        $tmp = [];
        foreach (Arr::get($arr, "data") as $inquiry) {
            $tmp[] = Inquiry::from(["data" => $inquiry]);
        }
        $this->inquiries = $tmp;
    }

    protected static function requiredKeys(): array
    {
        return [
            "data",
            "links"
        ];
    }

    public static function from(array $array): PaginatedInquiries
    {
        return self::fromExtended($array, null, false);
    }

    /**
     * @return PaginatedInquiries
     * @throws NoNextPage
     */
    public function nextPage(): PaginatedInquiries
    {
        return parent::nextPage();
    }

    /**
     * @return PaginatedInquiries
     * @throws NoPreviousPage
     */
    public function previousPage(): PaginatedInquiries
    {
        return parent::previousPage();
    }
}

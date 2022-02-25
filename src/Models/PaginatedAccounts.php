<?php

namespace Doinc\PersonaKyc\Models;

use ArrayAccess;
use Doinc\PersonaKyc\Exceptions\NoNextPage;
use Doinc\PersonaKyc\Exceptions\NoPreviousPage;
use Illuminate\Support\Arr;

class PaginatedAccounts extends PersonaPagination implements IPersonaModel
{
    use PersonaModel;

    /** @var Account[]  */
    public readonly array $accounts;

    private function __construct(array $arr)
    {
        parent::__construct($arr);

        $tmp = [];
        foreach (Arr::get($arr, "data") as $account) {
            $tmp[] = Account::from(["data" => $account]);
        }
        $this->accounts = $tmp;
    }

    protected static function requiredKeys(): array
    {
        return [
            "data",
            "links"
        ];
    }

    public static function from(array $array): PaginatedAccounts
    {
        return self::fromExtended($array, null, false);
    }

    /**
     * @return PaginatedAccounts
     * @throws NoNextPage
     */
    public function nextPage(): PaginatedAccounts
    {
        return parent::nextPage();
    }

    /**
     * @return PaginatedAccounts
     * @throws NoPreviousPage
     */
    public function previousPage(): PaginatedAccounts
    {
        return parent::previousPage();
    }
}

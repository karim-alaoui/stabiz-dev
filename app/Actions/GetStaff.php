<?php


namespace App\Actions;


use App\Models\Staff;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;

/**
 * Class GetStaff
 * @package App\Actions
 */
class GetStaff
{
    /**
     * @param array $query
     * @return LengthAwarePaginator
     */
    public static function execute(array $query = []): LengthAwarePaginator
    {
        $staff = Staff::query();

        $where = [];
        $id = Arr::get($query, 'id');
        if ($id) $where[] = ['id', $id];

        $firstName = Arr::get($query, 'first_name');
        if ($firstName) $where[] = ['first_name', 'ilike', "%$firstName%"];

        $lastName = Arr::get($query, 'last_name');
        if ($lastName) $where[] = ['last_name', 'ilike', "%$lastName%"];

        $email = Arr::get($query, 'email');
        if ($email) $where[] = ['email', 'ilike', "%$email%"];

        return $staff->where($where)
            ->with(['roles'])
            ->paginate(
                perPage: Arr::get($query, 'per_page', 15),
                page: Arr::get($query, 'page', 1)
            );
    }
}

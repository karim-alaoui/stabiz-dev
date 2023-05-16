<?php


namespace App\Actions;


use App\Exceptions\ActionException;
use App\Models\User;
use App\Traits\RelationshipTrait;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;

/**
 * Class GetUsers
 * @package App\Actions
 */
class GetUsers
{
    use RelationshipTrait;

    /**
     * @param string|array $type - user type - entrepreneur or founder or both
     * @param array $query
     * @return LengthAwarePaginator
     * @throws ActionException
     */
    public static function execute(string|array $type, array $query = []): LengthAwarePaginator
    {
        $types = [User::ENTR, User::FOUNDER];
        if (gettype($type) == 'string' && !in_array($type, $types)) {
            throw new ActionException(__('Type has to either entrepreneur or founder'));
        }

        $searchType = gettype($type) == 'string' ? [$type] : $type;
        $where = [];
        $firstName = Arr::get($query, 'first_name');
        if ($firstName) $where[] = ['first_name', 'ilike', "%$firstName%"];

        $lastName = Arr::get($query, 'last_name');
        if ($lastName) $where[] = ['last_name', 'ilike', "%$lastName%"];

        $users = User::query()
            ->whereIn('type', $searchType)
            ->where($where);

        // if front end adds 1 or true as string, then add it. It means true
        $assignedStaff = Arr::get($query, 'add_assigned_staff');
        if ($assignedStaff == 1 || $assignedStaff == 'true') {
            $users = $users->with(['assignedStaff' => fn($q) => $q->with('staff:id,first_name,last_name')]);
        }

        return $users->paginate(
            perPage: Arr::get($query, 'per_page', 15),
            page: Arr::get($query, 'page', 1)
        );
    }
}

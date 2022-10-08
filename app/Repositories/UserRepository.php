<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\Repositories\UserRepositoryInterface;
use App\Models\Base;
use App\Models\User;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Arr;

class UserRepository extends SingleKeyModelRepository implements UserRepositoryInterface
{
    public function getBlankModel(): Base|Builder
    {
        return new User();
    }

    public function rules(): array
    {
        return [
        ];
    }

    public function messages(): array
    {
        return [
        ];
    }

    protected function buildQueryByFilter(Builder $query, array $filter): Builder
    {
        if (\array_key_exists('query', $filter)) {
            $searchWord = Arr::get($filter, 'query');
            if (!empty($searchWord)) {
                $query = $query->where(function ($q) use ($searchWord): void {
                    $q->where('name', 'LIKE', '%'.$searchWord.'%');
                });
                unset($filter['query']);
            }
        }

        return parent::buildQueryByFilter($query, $filter);
    }
}

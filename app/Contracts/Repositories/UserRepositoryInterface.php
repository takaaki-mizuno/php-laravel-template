<?php

declare(strict_types=1);

namespace App\Contracts\Repositories;

use App\Models\Base;
use Illuminate\Database\Query\Builder;

/**
 * @method \App\Models\User[]                    getEmptyList()
 * @method \App\Models\User[]|array|\Traversable all($order = null, $direction = null)
 * @method \App\Models\User[]|array|\Traversable get($order, $direction, $offset, $limit)
 * @method \App\Models\User                      create(array $value)
 * @method \App\Models\User                      find(int $id)
 * @method \App\Models\User[]|array|\Traversable allByIds(array $ids, string $order = null, string $direction = null, bool $reorder = false)
 * @method  \App\Models\User[]|array|\Traversable getByIds(array $ids, string $order = null, string $direction = null, int $offset = null, int $limit = null);
 * @method \App\Models\User update(Base $model, array $input)
 * @method  \App\Models\User save(Base $model);
 * @method  \App\Models\User firstByFilter($filter);
 * @method  \App\Models\User[]|array|\Traversable getByFilter($filter,$order = null, $direction = null, $offset = null, $limit = null);
 * @method  \App\Models\User[]|array|\Traversable allByFilter($filter,$order = null, $direction = null);
 */
interface UserRepositoryInterface extends SingleKeyModelRepositoryInterface
{
    public function getBlankModel(): Base|Builder;
}

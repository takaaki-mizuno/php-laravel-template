<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\User;

use App\Exceptions\Api\User\APIErrorException;
use App\Http\Requests\Request as BaseRequest;
use Illuminate\Contracts\Validation\Validator;

class Request extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [];
    }

    public function messages(): array
    {
        return [];
    }

    public function onlyNonNull($keys): array
    {
        $data = parent::only($keys);
        $result = [];
        foreach ($data as $key => $value) {
            if (null !== $value) {
                $result[$key] = $value;
            }
        }

        return $result;
    }

    /**
     * @throws APIErrorException
     */
    protected function failedValidation(Validator $validator): void
    {
        $transformed = [];

        $errors = $validator->errors();

        foreach ($errors->keys() as $key) {
            $transformed[] = [
                'name' => $key,
                'message' => $errors->get($key, [])[0],
            ];
        }

        throw new APIErrorException('wrongParameter', 'Wrong Parameters', ['invalidParams' => $transformed]);
    }
}

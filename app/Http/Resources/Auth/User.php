<?php

namespace App\Http\Resources\Auth;

use App\Libraries\AppLibrary;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class User extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                => $this->id,
            'name'              => $this->name,
            'email'             => $this->email,
            'username'          => $this->username,
            'phone'             => $this->phone,
            'balance'           => AppLibrary::flatAmountFormat($this->balance),
            'currencyBalance'   => AppLibrary::currencyAmountFormat($this->balance),
            'createdAt'         => AppLibrary::formatDateTime($this->created_at),
            'updatedAt'         => AppLibrary::formatDateTime($this->updated_at)
        ];
    }
}

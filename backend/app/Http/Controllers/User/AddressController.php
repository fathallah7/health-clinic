<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\AddressRequest;
use App\Models\UserAddress;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class AddressController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $patient = $request->user();
        $address = $patient->address()->get();
        return $this->success($address, 'User Address');
    }

    public function store(Request $request, AddressRequest $addressRequest)
    {
        $patient = $request->user();

        $validated = $addressRequest->validated();
        $address = $patient->address()->create($validated);

        return $this->success($address, 'Address created', 201);
    }

    public function update(Request $request, AddressRequest $addressRequest, UserAddress $userAddress)
    {
        if (Gate::denies('update', $userAddress)) {
            return $this->error('You do not have permission to update this address', [], 403);
        }

        $validated = $addressRequest->validated(); 
        $userAddress->update($validated);

        return $this->success($userAddress, 'Address updated');
    }

    public function destroy(UserAddress $userAddress)
    {
        if (Gate::denies('delete', $userAddress)) {
            return $this->error('You do not have permission to update this address', [], 403);
        }

        $userAddress->delete();
        return $this->success(null, 'Address deleted');
    }
}

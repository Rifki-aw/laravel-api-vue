<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Address;
use App\Models\Contact;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\AddressResource;
use App\Http\Requests\AddressCreateRequest;
use App\Http\Requests\AddressUpdateRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class AddressController extends Controller
{
    private function getContact(User $user, int $idContact): Contact 
    {
        // Ambil data contact yang sesuai dengan user yang sedang login dan ID contact yang diberikan
        $contact = Contact::where('user_id', $user->id)->where('id', $idContact)->first();
        
        // Cek jika data contact tidak ditemukan di database
        if (!$contact) {
            // Jika contact tidak ditemukan, lemparkan error 404 dengan pesan "not found"
            throw new HttpResponseException(response()->json([
                "errors" => [
                    "message" => [
                        "not found"
                    ]
                ]
            ])->setStatusCode(404));
        }

        return $contact;
    }

    private function getAddress(Contact $contact, int $idAddress): Address
    {
        // Ambil data address yang sesuai dengan contact
        $address = Address::where('contact_id', $contact->id)->where('id', $idAddress)->first();

        // Cek jika data contact tidak ditemukan di database
        if (!$address) {
            // Jika contact tidak ditemukan, lemparkan error 404 dengan pesan "not found"
            throw new HttpResponseException(response()->json([
                "errors" => [
                    "message" => [
                        "not found"
                    ]
                ]
            ])->setStatusCode(404));
        }

        return $address;
    }

    public function create(int $idContact, AddressCreateRequest $request): JsonResponse
    {
        // Cek dan ambil user yang sedang login
        $user = Auth::user();

        // Ambil data contact yang sesuai dengan user yang sedang login dan ID contact yang diberikan
        $contact = $this->getContact($user, $idContact);

        // Ambil data yang sudah tervalidasi dari request
        $data = $request->validated();

        // Definisikan dan buat instance baru dari model Address dengan data yang sudah tervalidasi
        $address = new Address($data);
        // Set contact_id pada Address sesuai dengan ID contact yang ditemukan
        $address->contact_id = $contact->id;
        // Simpan Address baru
        $address->save();

        return (new AddressResource($address))->response()->setStatusCode(201);
    }

    public function get(int $idContact, int $idAddress): AddressResource
    {
        // Cek dan ambil user yang sedang login
        $user = Auth::user();

        // Ambil data contact yang sesuai dengan user yang sedang login dan ID contact yang diberikan
        $contact = $contact = $this->getContact($user, $idContact);

        // Ambil data address yang sesuai dengan contact
        $address = $this->getAddress($contact, $idAddress);

        return new AddressResource($address);
    }

    public function update(int $idContact, int $idAddress, AddressUpdateRequest $request): AddressResource 
    {
        // Cek dan ambil user yang sedang login
        $user = Auth::user();

        // Ambil data contact yang sesuai dengan user yang sedang login dan ID contact yang diberikan
        $contact = $contact = $this->getContact($user, $idContact);

        // Ambil data address yang sesuai dengan contact
        $address = $this->getAddress($contact, $idAddress);

        $data = $request->validated();
        $address->fill($data);
        $address->save();

        return new AddressResource($address);
    }

    public function delete(int $idContact, int $idAddress): JsonResponse
    {
        // Cek dan ambil user yang sedang login
        $user = Auth::user();

        // Ambil data contact yang sesuai dengan user yang sedang login dan ID contact yang diberikan
        $contact = $contact = $this->getContact($user, $idContact);

        // Ambil data address yang sesuai dengan contact
        $address = $this->getAddress($contact, $idAddress);

        $address->delete();
        return response()->json([
            "data" => true
        ])->setStatusCode(200);
    }

    public function list(int $idContact): JsonResponse
    {
        // Cek dan ambil user yang sedang login
        $user = Auth::user();

        // Ambil data contact yang sesuai dengan user yang sedang login dan ID contact yang diberikan
        $contact = $contact = $this->getContact($user, $idContact);

        $addresses = Address::where('contact_id', $contact->id)->get();

        return (AddressResource::collection($addresses))->response()->setStatusCode(200);
    }
}

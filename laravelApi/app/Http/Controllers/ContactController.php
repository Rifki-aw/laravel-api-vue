<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactCreateRequest;
use App\Http\Requests\ContactUpdateRequest;
use App\Http\Resources\ContactResource;
use App\Models\Contact;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

class ContactController extends Controller
{
    public function create(ContactCreateRequest $request): JsonResponse
    {
        $data = $request->validated();

        // ambil user yang sedang login
        $user = Auth::user();

        $contact = new Contact($data);
        $contact->user_id = $user->id;
        $contact->save();

        return (new ContactResource($contact))->response()->setStatusCode(201);
    }

    public function get(int $id): ContactResource
    {
        $user = Auth::user();

        $contact = Contact::where('id', $id)->where('user_id', $user->id)->first();

        // cek jika data contact tidak ada di DB
        if (!$contact) {
            throw new HttpResponseException(response()->json([
                "errors" => [
                    "message" => [
                        "User not found"
                    ]
                ]
            ])->setStatusCode(404));
        }

        return new ContactResource($contact);
    }

    public function update(int $id, ContactUpdateRequest $request): ContactResource
    {
        $user = Auth::user();
        $contact = Contact::where('id', $id)->where('user_id', $user->id)->first();

        // cek jika data contact tidak ada di DB
        if (!$contact) {
            throw new HttpResponseException(response()->json([
                "errors" => [
                    "message" => [
                        "not found"
                    ]
                ]
            ])->setStatusCode(404));
        }

        // ambil data
        $data = $request->validated();

        // masukkan data ke dalam kontak
        $contact->fill($data);

        // perbarui kontak
        $contact->save();

        return new ContactResource($contact);
    }

    public function delete(int $id): JsonResponse
    {
        $user = Auth::user();
        $contact = Contact::where('id', $id)->where('user_id', $user->id)->first();

        // cek jika data contact tidak ada di DB
        if (!$contact) {
            throw new HttpResponseException(response()->json([
                "errors" => [
                    "message" => [
                        "not found"
                    ]
                ]
            ])->setStatusCode(404));
        }

        $contact->delete();
        return response()->json([
            "data" => true
        ])->setStatusCode(200);
    }
}

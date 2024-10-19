<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactCreateRequest;
use App\Http\Requests\ContactUpdateRequest;
use App\Http\Resources\ContactCollection;
use App\Http\Resources\ContactResource;
use App\Models\Contact;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

    public function search(Request $request): ContactCollection
    {
        $user = Auth::user();

        // cari halaman atau page berapa
        $page = $request->input('page', 1);

        // definisi berapa banyak data yang dapat ditampilkan dalam 1 halaman
        $size = $request->input('size', 10);

        $contacts = Contact::query()->where('user_id', $user->id);

        $contacts = $contacts->where(function (Builder $builder) use ($request) {
            // jika ada request berdasarkan name
            $name = $request->input('name');
            if ($name) {
                // cek harus ada nama dari last atau first name
                $builder->where(function (Builder $builder) use ($name) {
                    $builder->orWhere('first_name', 'like', '%' . $name . '%');
                    $builder->orWhere('last_name', 'like', '%' . $name . '%');
                });
            }

            // jika ada request berdasarkan email
            $email = $request->input('email');
            if ($email) {
                $builder->where('email', 'like', '%' . $email . '%');
            }

            // jika ada request berdasarkan phone
            $phone = $request->input('phone');
            if ($phone) {
                $builder->where('phone', 'like', '%' . $phone . '%');
            }
        });

        // setelah dicek lalu ambil melalui pagination
        $contacts = $contacts->paginate(perPage: $size, page: $page);

        return new ContactCollection($contacts);

        // VERSI GPT
        // $contacts = Contact::where('user_id', $user->id);

        // tambahkan kondisi pencarian berdasarkan nama, email, dan telepon
        // $contacts = $contacts->when($request->input('name'), function (Builder $builder, $name) {
        //     $builder->where(function (Builder $builder) use ($name) {
        //         $builder->orWhere('first_name', 'like', '%' . $name . '%')
        //             ->orWhere('last_name', 'like', '%' . $name . '%');
        //     });
        // })
        //     ->when($request->input('email'), function (Builder $builder, $email) {
        //         $builder->where('email', 'like', '%' . $email . '%');
        //     })
        //     ->when($request->input('phone'), function (Builder $builder, $phone) {
        //         $builder->where('phone', 'like', '%' . $phone . '%');
        //     });

        // // ambil melalui pagination
        // $contacts = $contacts->paginate(perPage: $size, page: $page);

        // return new ContactCollection($contacts);
    }
}

<?php

namespace App\Repositories;

use App\DTOs\NidData;
use App\Models\ServerCopy;

/**
 * ServerCopyRepository
 *
 * All database access for the server-copy feature lives here.
 * The service layer never touches Eloquent directly.
 */
class ServerCopyRepository
{
    /**
     * Find an existing record by NID + DOB.
     */
    public function findByNidAndDob(string $nid, string $dob): ?ServerCopy
    {
        return ServerCopy::byNidAndDob($nid, $dob)->first();
    }

    /**
     * Persist a NidData DTO (upsert: update if already saved, insert if new).
     * Returns the saved model.
     */
    public function save(NidData $data): ServerCopy
    {
        return ServerCopy::updateOrCreate(
            [
                'nid' => $data->nid,
                'dob' => $data->dob,
            ],
            [
                'name'            => $data->name,
                'name_en'         => $data->nameEn,
                'pin'             => $data->pin,
                'father'          => $data->father,
                'father_nid'      => $data->fatherNid,
                'mother'          => $data->mother,
                'mother_nid'      => $data->motherNid,
                'spouse'          => $data->spouse,
                'blood_group'     => $data->bloodGroup,
                'gender'          => $data->gender,
                'birth_place'     => $data->birthPlace,
                'religion'        => $data->religion,
                'occupation'      => $data->occupation,
                'mobile'          => $data->mobile,
                'voter_no'        => $data->voterNo,
                'voter_area'      => $data->voterArea,
                'voter_area_code' => $data->voterAreaCode,
                'sl_no'           => $data->slNo,
                'pre_address_line'=> $data->preAddressLine,
                'per_address_line'=> $data->perAddressLine,
                'photo'           => $data->photo,
                'raw_data'        => $data->toArray(),
            ]
        );
    }
}

<?php

namespace App\DTOs;

/**
 * NidData DTO
 *
 * Mirrors the TypeScript INidDataV2 interface exactly.
 * This is the canonical shape for NID data flowing through the system.
 */
class NidData
{
    public function __construct(
        // Names
        public readonly string  $name,
        public readonly string  $nameEn,

        // IDs
        public readonly string  $nid,
        public readonly string  $pin,

        // Personal
        public readonly string  $dob,
        public readonly string  $father,
        public readonly ?string $fatherNid,
        public readonly string  $mother,
        public readonly ?string $motherNid,
        public readonly ?string $spouse,
        public readonly ?string $bloodGroup,
        public readonly ?string $gender,
        public readonly ?string $birthPlace,
        public readonly ?string $religion,
        public readonly ?string $occupation,
        public readonly ?string $mobile,

        // Voter
        public readonly ?string $voterNo,
        public readonly ?string $voterArea,
        public readonly ?int    $voterAreaCode,
        public readonly ?int    $slNo,

        // Addresses (pre-built strings)
        public readonly string  $preAddressLine,
        public readonly string  $perAddressLine,

        // Photo
        public readonly ?string $photo,
    ) {}

    /**
     * Build from a raw API response array.
     * Handles the V2 API shape: Success="True" with flat fields.
     */
    public static function fromApiResponse(array $raw): self
    {
        return new self(
            name:           $raw['name']          ?? '',
            nameEn:         $raw['nameEn']         ?? '',
            nid:            $raw['nid']            ?? '',
            pin:            $raw['pin']            ?? '',
            dob:            $raw['dob']            ?? '',
            father:         $raw['father']         ?? '',
            fatherNid:      $raw['fatherNid']      ?? null,
            mother:         $raw['mother']         ?? '',
            motherNid:      $raw['motherNid']      ?? null,
            spouse:         $raw['spouse']         ?? null,
            bloodGroup:     $raw['bloodGroup']     ?? null,
            gender:         $raw['gender']         ?? null,
            birthPlace:     $raw['birthPlace']     ?? null,
            religion:       $raw['religion']       ?? null,
            occupation:     $raw['occupation']     ?? null,
            mobile:         $raw['mobile']         ?? null,
            voterNo:        $raw['voterNo']        ?? null,
            voterArea:      $raw['voterArea']      ?? null,
            voterAreaCode:  isset($raw['voterAreaCode']) ? (int) $raw['voterAreaCode'] : null,
            slNo:           isset($raw['slNo'])    ? (int) $raw['slNo'] : null,
            preAddressLine: $raw['preAddressLine'] ?? '',
            perAddressLine: $raw['perAddressLine'] ?? '',
            photo:          $raw['photo']          ?? null,
        );
    }

    /**
     * Build from an Eloquent ServerCopy model instance.
     */
    public static function fromModel(\App\Models\ServerCopy $model): self
    {
        return new self(
            name:           $model->name           ?? '',
            nameEn:         $model->name_en        ?? '',
            nid:            $model->nid,
            pin:            $model->pin            ?? '',
            dob:            $model->dob,
            father:         $model->father         ?? '',
            fatherNid:      $model->father_nid,
            mother:         $model->mother         ?? '',
            motherNid:      $model->mother_nid,
            spouse:         $model->spouse,
            bloodGroup:     $model->blood_group,
            gender:         $model->gender,
            birthPlace:     $model->birth_place,
            religion:       $model->religion,
            occupation:     $model->occupation,
            mobile:         $model->mobile,
            voterNo:        $model->voter_no,
            voterArea:      $model->voter_area,
            voterAreaCode:  $model->voter_area_code,
            slNo:           $model->sl_no,
            preAddressLine: $model->pre_address_line ?? '',
            perAddressLine: $model->per_address_line ?? '',
            photo:          $model->photo,
        );
    }

    /**
     * Convert to array for JSON responses.
     */
    public function toArray(): array
    {
        return [
            'name'           => $this->name,
            'nameEn'         => $this->nameEn,
            'nid'            => $this->nid,
            'pin'            => $this->pin,
            'dob'            => $this->dob,
            'father'         => $this->father,
            'fatherNid'      => $this->fatherNid,
            'mother'         => $this->mother,
            'motherNid'      => $this->motherNid,
            'spouse'         => $this->spouse,
            'bloodGroup'     => $this->bloodGroup,
            'gender'         => $this->gender,
            'birthPlace'     => $this->birthPlace,
            'religion'       => $this->religion,
            'occupation'     => $this->occupation,
            'mobile'         => $this->mobile,
            'voterNo'        => $this->voterNo,
            'voterArea'      => $this->voterArea,
            'voterAreaCode'  => $this->voterAreaCode,
            'slNo'           => $this->slNo,
            'preAddressLine' => $this->preAddressLine,
            'perAddressLine' => $this->perAddressLine,
            'photo'          => $this->photo,
        ];
    }
}

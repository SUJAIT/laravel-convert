<?php

namespace App\Services;

use App\DTOs\NidData;
use App\Repositories\ServerCopyRepository;


class ServerCopyService
{
    public function __construct(
        private readonly NidApiService        $api,
        private readonly ServerCopyRepository $repo,
    ) {}

    // ─────────────────────────────────────────────────────────────────────────

    /**
   
     
     * @throws \RuntimeException  NID_NOT_FOUND | NID_API_ERROR
     */
    public function search(string $nid, string $dob): NidData
    {
        // Fetch from external API (throws on failure)
        $data = $this->api->fetch($nid, $dob);

        // Persist to DB so PDF download never needs an API call
        $this->repo->save($data);

        return $data;
    }


    public function getForPdf(string $nid, string $dob): ?NidData
    {
        $model = $this->repo->findByNidAndDob($nid, $dob);

        if (! $model) {
            return null;
        }

        return NidData::fromModel($model);
    }
}

<?php

namespace App\Services;

use App\DTOs\NidData;
use Spatie\Browsershot\Browsershot;


class PdfGeneratorService
{
    /**
     * @return string  Raw PDF binary
     * @throws \Exception
     */
    public function generate(NidData $data): string
    {
        // Fetch photo and QR as base64 in parallel (via Guzzle promises)
        $photoBase64 = $this->urlToBase64($data->photo, 'image/jpeg');
        $bgBase64    = $this->fileToBase64(public_path('assets/images/cbimage.png'), 'image/png');
        $qrBase64    = $this->buildQrBase64($data);

        $html = $this->buildHtml($data, $bgBase64, $photoBase64, $qrBase64);

return Browsershot::html($html)
    ->noSandbox()
    ->disableGpu()
    ->format('A4')                
    ->showBackground()
    ->margins(0, 0, 0, 0)
    ->pdf();
    }

    // HTML builder


    private function buildHtml(
        NidData $data,
        string  $bgBase64,
        string  $photoBase64,
        string  $qrBase64
    ): string {
        $safe = fn(?string $v) => ($v !== null && trim($v) !== '') ? htmlspecialchars($v) : 'N/A';
        $nid  = htmlspecialchars($data->nid);

        $photoTag = $photoBase64
            ? '<img src="' . $photoBase64 . '" height="140px" width="121px" style="border-radius:10px"/>'
            : '<div style="width:121px;height:140px;background:#eee;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:11px;color:#999;">No Photo</div>';

        $qrTag = $qrBase64
            ? '<img src="' . $qrBase64 . '" height="100px" width="100px"/>'
            : '<div style="width:100px;height:100px;border:1px solid #999;">QR</div>';

        $bloodColor = 'rgb(247, 0, 0)';

        // Font paths — SolaimanLipi embedded as base64 for Bengali rendering
        $fontNormal = $this->fileToBase64(public_path('assets/fonts/SolaimanLipi-Normal.ttf'), 'font/truetype');
        $fontBold   = $this->fileToBase64(public_path('assets/fonts/SolaimanLipi-Bold.ttf'),   'font/truetype');

        return <<<HTML
<!DOCTYPE html>
<html lang="bn">
<head>
  <meta charset="UTF-8"/>
  <style>
    @font-face {
      font-family: 'SolaimanLipi';
      src: url({$fontNormal}) format('truetype');
      font-weight: normal; font-style: normal;
    }
    @font-face {
      font-family: 'SolaimanLipi';
      src: url({$fontBold}) format('truetype');
      font-weight: bold; font-style: normal;
    }
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body {
      text-align: center; margin: 0; padding: 0;
      font-family: 'SolaimanLipi', Arial, sans-serif !important;
      -webkit-font-smoothing: antialiased;
    }
    .background {
      position: relative; width: 750px; height: 1000px; margin: auto; text-align: left;
    }
    .crane { position: absolute; top: 0; left: 0; z-index: 0; }
    .abs {
      position: absolute; font-family: 'SolaimanLipi', sans-serif !important;
      color: rgb(7, 7, 7); z-index: 1;
    }
    b, strong { font-weight: bold; }
  </style>
</head>
<body>
  <div class="background">
    <img class="crane" src="{$bgBase64}" height="1000px" width="750px">

    <div class="abs" style="left:30%;top:6.2%;font-size:16px;color:rgb(255,224,0);"><b>National Identity Registration Wing (NIDW)</b></div>
    <div class="abs" style="left:37%;top:9.2%;font-size:14px;color:rgb(255,47,161);"><b>Select Your Search Category</b></div>
    <div class="abs" style="left:45%;top:11.2%;font-size:12px;color:rgb(8,121,4);">Search By NID / Voter No.</div>
    <div class="abs" style="left:45%;top:12.5%;font-size:12px;color:rgb(7,119,184);">Search By Form No.</div>
    <div class="abs" style="left:30%;top:15.2%;font-size:12px;color:rgb(252,0,0);"><b>NID or Voter No*</b></div>
    <div class="abs" style="left:45%;top:15.2%;font-size:12px;color:rgb(143,143,143);">{$nid}</div>
    <div class="abs" style="left:62.9%;top:15.4%;font-size:11px;color:#fff;">Submit</div>
    <div class="abs" style="left:89%;top:9.9%;font-size:11px;color:#fff;">Home</div>

    <div class="abs" style="left:37.5%;top:25.4%;font-size:16px;"><b>জাতীয় পরিচিতি তথ্য</b></div>
    <div class="abs" style="left:37.3%;top:28.3%;font-size:13px;">জাতীয় পরিচয় পত্র নম্বর</div>
    <div class="abs" style="left:55%;top:28.3%;font-size:14px;">{$nid}</div>
    <div class="abs" style="left:37.3%;top:30.9%;font-size:13px;">পিন নম্বর</div>
    <div class="abs" style="left:55%;top:30.9%;font-size:14px;">{$safe($data->pin)}</div>
    <div class="abs" style="left:37.3%;top:33.5%;font-size:13px;">ভোটার নম্বর</div>
    <div class="abs" style="left:55%;top:33.5%;font-size:14px;">{$safe($data->voterNo)}</div>
    <div class="abs" style="left:37.3%;top:36.2%;font-size:14px;">সিরিয়াল নম্বর</div>
    <div class="abs" style="left:55%;top:36.2%;font-size:14px;">{$safe($data->slNo !== null ? (string)$data->slNo : null)}</div>
    <div class="abs" style="left:37.3%;top:38.8%;font-size:14px;">ভোটার এলাকা নম্বর</div>
    <div class="abs" style="left:55%;top:38.8%;font-size:14px;">{$safe($data->voterAreaCode !== null ? (string)$data->voterAreaCode : null)}</div>

    <div class="abs" style="left:37.5%;top:41.5%;font-size:16px;"><b>ব্যক্তিগত তথ্য</b></div>
    <div class="abs" style="left:37.3%;top:44.2%;font-size:14px;">নাম (বাংলা)</div>
    <div class="abs" style="left:55%;top:44.2%;font-size:14px;font-weight:bold;">{$safe($data->name)}</div>
    <div class="abs" style="left:37.3%;top:46.9%;font-size:14px;">নাম (ইংরেজি)</div>
    <div class="abs" style="left:55%;top:46.9%;font-size:14px;">{$safe($data->nameEn)}</div>
    <div class="abs" style="left:37.3%;top:49.5%;font-size:14px;">জন্ম তারিখ</div>
    <div class="abs" style="left:55%;top:49.5%;font-size:14px;">{$safe($data->dob)}</div>
    <div class="abs" style="left:37.3%;top:52.2%;font-size:14px;">পিতার নাম</div>
    <div class="abs" style="left:55%;top:52.2%;font-size:14px;">{$safe($data->father)}</div>
    <div class="abs" style="left:37.3%;top:54.7%;font-size:14px;">মাতার নাম</div>
    <div class="abs" style="left:55%;top:54.7%;font-size:14px;">{$safe($data->mother)}</div>
    <div class="abs" style="left:37.3%;top:57.5%;font-size:14px;">স্বামী / স্ত্রীর নাম</div>
    <div class="abs" style="left:55%;top:57.5%;font-size:14px;">{$safe($data->spouse)}</div>

    <div class="abs" style="left:37.5%;top:60%;font-size:16px;"><b>অন্যান্য তথ্য</b></div>
    <div class="abs" style="left:37.3%;top:63.4%;font-size:14px;">লিঙ্গ</div>
    <div class="abs" style="left:55%;top:63.4%;font-size:14px;">{$safe($data->gender)}</div>
    <div class="abs" style="left:37.3%;top:66%;font-size:14px;">ধর্ম</div>
    <div class="abs" style="left:55%;top:66%;font-size:14px;">{$safe($data->religion)}</div>
    <div class="abs" style="left:37.3%;top:68.6%;font-size:14px;">জন্মস্থান</div>
    <div class="abs" style="left:55%;top:68.6%;font-size:14px;">{$safe($data->birthPlace)}</div>
    <div class="abs" style="left:37.3%;top:71.3%;font-size:14px;">রক্তের গ্রুপ</div>
    <div class="abs" style="left:55%;top:71.3%;font-size:14px;color:{$bloodColor};font-weight:bold;">{$safe($data->bloodGroup)}</div>

    <div class="abs" style="left:37.5%;top:74%;font-size:16px;"><b>বর্তমান ঠিকানা</b></div>
    <div class="abs" style="left:37.3%;top:76.8%;width:48%;font-size:11px;line-height:1.5;">{$safe($data->preAddressLine)}</div>

    <div class="abs" style="left:37.5%;top:82.9%;font-size:16px;"><b>স্থায়ী ঠিকানা</b></div>
    <div class="abs" style="left:37.3%;top:85.7%;width:48%;font-size:11px;line-height:1.5;">{$safe($data->perAddressLine)}</div>

    <div style="position:absolute;top:93%;width:100%;font-size:11px;text-align:center;color:red;">উপরে প্রদর্শিত তথ্যসমূহ জাতীয় পরিচয়পত্র সংশ্লিষ্ট, ভোটার তালিকার সাথে সরাসরি সম্পর্কযুক্ত নয়।</div>
    <div style="position:absolute;top:94.5%;width:100%;text-align:center;font-size:11px;color:rgb(3,3,3);">This is Software Generated Report From Bangladesh Election Commission, Signature &amp; Seal Aren't Required.</div>

    <div class="abs" style="left:16%;top:24%;">{$photoTag}</div>
    <div class="abs" style="left:15.5%;top:38.7%;width:130px;height:32px;display:flex;align-items:center;justify-content:center;font-weight:bold;font-size:13px;text-align:center;">
      <div style="flex:1;">{$safe($data->nameEn)}</div>
    </div>

    <div class="abs" style="left:17.5%;top:42.5%;">
      {$qrTag}
    </div>
  </div>
</body>
</html>
HTML;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Helpers
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Read a local file and return as a data-URI base64 string.
     * Returns empty string if the file doesn't exist.
     */
    private function fileToBase64(string $path, string $mimeType): string
    {
        if (! file_exists($path)) {
            return '';
        }

        $base64 = base64_encode(file_get_contents($path));
        return "data:{$mimeType};base64,{$base64}";
    }

    /**
     * Fetch a remote URL and return as a data-URI base64 string.
     * Returns empty string on failure.
     */
    private function urlToBase64(?string $url, string $mimeType): string
    {
        if (empty($url)) {
            return '';
        }

        try {
            $response = \Illuminate\Support\Facades\Http::timeout(10)->get($url);
            if (! $response->successful()) {
                return '';
            }
            $base64 = base64_encode($response->body());
            return "data:{$mimeType};base64,{$base64}";
        } catch (\Exception) {
            return '';
        }
    }

    /**
     * Build QR code as a data-URI base64 string using qrserver.com API.
     */
    private function buildQrBase64(NidData $data): string
    {
        $text = urlencode("{$data->nameEn} {$data->nid} {$data->dob}");
        $url  = "https://api.qrserver.com/v1/create-qr-code/?ecc=L&size=100x100&data={$text}";
        return $this->urlToBase64($url, 'image/png');
    }
}

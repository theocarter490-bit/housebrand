<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GeneralSettingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'site_name' => $this->site_name,
            'site_title' => $this->site_title,
            'copyright_text' => $this->copyright_text,
            'currency' =>collect([
                'name' => $this->currency->name,
                'code' => $this->currency->code,
                'symbol' => $this->currency->symbol,
            ]),
            'timezone' => collect([
                'code' =>$this->timezone->code,
                'time_zone' =>$this->timezone->time_zone,
            ]),
            'date_format' => collect([
                'format' => $this->DateFormat->format,
            ]),
            'global_settings' => collect([
                'category_design'   => globalSetting('category_design')->value == 0 ? json_decode(globalSetting('category_design')->options)[0] : json_decode(globalSetting('category_design')->options)[1],
                'filter_option'     => globalSetting('filter_option')->value == 0 ? json_decode(globalSetting('filter_option')->options)[0] : json_decode(globalSetting('filter_option')->options)[1],
            ]),
        ];

    }
}

<?php

namespace Doinc\PersonaKyc\Models;

use Carbon\Carbon;
use Doinc\PersonaKyc\Enums\InquiryStatus;
use Doinc\PersonaKyc\Enums\ThreatLevels;
use Doinc\PersonaKyc\Exceptions\InvalidModelData;
use Illuminate\Support\Arr;

class InquirySession implements IPersonaModel
{
    use PersonaModel;

    public readonly string $id;
    public readonly InquiryStatus $status;
    public readonly Carbon $created_at;
    public readonly string $ip_address;
    public readonly string $user_agent;
    public readonly string $os_name;
    public readonly ?string $os_full_version;
    public readonly string $device_type;
    public readonly ?string $device_name;
    public readonly string $browser_name;
    public readonly string $browser_full_version;
    public readonly ?string $mobile_sdk_name;
    public readonly ?string $mobile_sdk_full_version;
    public readonly bool $is_proxy;
    public readonly bool $is_tor;
    public readonly bool $is_datacenter;
    public readonly ?ThreatLevels $threat_level;
    public readonly ?string $country_code;
    public readonly ?string $country_name;
    public readonly ?string $region_code;
    public readonly ?string $region_name;
    public readonly string $latitude;
    public readonly string $longitude;
    public readonly ?string $browser_fingerprint;

    private function __construct(array $arr)
    {
        $this->id = Arr::get($arr, "data.id");
        $this->status = InquiryStatus::from(Arr::get($arr, "data.attributes.status"));
        $this->created_at = Carbon::parse(Arr::get($arr, "data.attributes.created_at"));
        $this->ip_address = Arr::get($arr, "data.attributes.ip_address");
        $this->user_agent = Arr::get($arr, "data.attributes.user_agent");
        $this->os_name = Arr::get($arr, "data.attributes.os_name");
        $this->os_full_version = Arr::get($arr, "data.attributes.os_full_version");
        $this->device_type = Arr::get($arr, "data.attributes.device_type");
        $this->device_name = Arr::get($arr, "data.attributes.device_name");
        $this->browser_name = Arr::get($arr, "data.attributes.browser_name");
        $this->browser_full_version = Arr::get($arr, "data.attributes.browser_full_version");
        $this->mobile_sdk_name = Arr::get($arr, "data.attributes.mobile_sdk_name");
        $this->mobile_sdk_full_version = Arr::get($arr, "data.attributes.mobile_sdk_full_version");
        $this->is_proxy = (bool)Arr::get($arr, "data.attributes.is_proxy");
        $this->is_tor = (bool)Arr::get($arr, "data.attributes.is_tor");
        $this->is_datacenter = (bool)Arr::get($arr, "data.attributes.is_datacenter");
        $this->threat_level = !is_null(Arr::get($arr, "data.attributes.threat_level")) ? ThreatLevels::from(Arr::get($arr, "data.attributes.threat_level")) : null;
        $this->country_code = Arr::get($arr, "data.attributes.country_code");
        $this->country_name = Arr::get($arr, "data.attributes.country_name");
        $this->region_code = Arr::get($arr, "data.attributes.region_code");
        $this->region_name = Arr::get($arr, "data.attributes.region_name");
        $this->latitude = "" . Arr::get($arr, "data.attributes.latitude");
        $this->longitude = "" . Arr::get($arr, "data.attributes.longitude");
        $this->browser_fingerprint = Arr::get($arr, "data.attributes.browser_fingerprint");
    }

    private static function requiredKeys(): array
    {
        return [
            "data.type",
            "data.id",
            "data.attributes",
            "data.attributes.status",
            "data.attributes.created_at",
            "data.attributes.ip_address",
            "data.attributes.user_agent",
            "data.attributes.os_name",
            "data.attributes.os_full_version",
            "data.attributes.device_type",
            "data.attributes.device_name",
            "data.attributes.browser_name",
            "data.attributes.browser_full_version",
            "data.attributes.mobile_sdk_name",
            "data.attributes.mobile_sdk_full_version",
            "data.attributes.is_proxy",
            "data.attributes.is_tor",
            "data.attributes.is_datacenter",
            "data.attributes.threat_level",
            "data.attributes.country_code",
            "data.attributes.country_name",
            "data.attributes.region_code",
            "data.attributes.region_name",
            "data.attributes.latitude",
            "data.attributes.longitude",
            "data.attributes.browser_fingerprint",
        ];
    }

    /**
     * Parse a json array returning a new Account instance
     *
     * @throws InvalidModelData
     */
    public static function from(array $array): InquirySession
    {
        return self::fromExtended($array, "inquiry-session");
    }
}

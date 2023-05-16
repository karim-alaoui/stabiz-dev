<?php

namespace App\Actions;

use App\Exceptions\ActionException;
use App\Models\FounderProfile;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * Update company logo and banner
 * This applies only to the founders since only founders have companies
 */
class UpdateCompanyImgs
{
    protected static ?UploadedFile $uploadedBannerImg = null;

    protected static bool $removeBanner = false;

    protected static ?UploadedFile $uploadedLogo = null;

    protected static bool $removeLogo = false;

    public static function uploadBanner(UploadedFile $uploadedFile = null): static
    {
        self::$uploadedBannerImg = $uploadedFile;
        return new static();
    }

    public static function removeBannerImg(bool $remove = false): static
    {
        self::$removeBanner = $remove;
        return new static();
    }

    public static function uploadLogo(UploadedFile $uploadedFile = null): static
    {
        self::$uploadedLogo = $uploadedFile;
        return new static();
    }

    public static function removeLogoImg(bool $remove = false): static
    {
        self::$removeLogo = $remove;
        return new static();
    }

    private static function removeLogo(FounderProfile $fdrProfile)
    {
        $disk = $fdrProfile->company_logo_disk;
        $path = $fdrProfile->company_logo_path;
        if (is_null($disk) || is_null($path)) return;

        $exists = Storage::disk($disk)
            ->exists($path);
        if ($exists) {
            Storage::disk($disk)
                ->delete($path);

            $fdrProfile->company_logo_path = null;
            $fdrProfile->company_logo_path = null;
            $fdrProfile->save();
        }
    }

    private static function removeBanner(FounderProfile $fdrProfile)
    {
        $disk = $fdrProfile->company_banner_disk;
        $path = $fdrProfile->company_banner_img_path;
        if (is_null($disk) || is_null($path)) return;

        $exists = Storage::disk($disk)
            ->exists($path);
        if ($exists) {
            Storage::disk($disk)
                ->delete($path);

            $fdrProfile->company_banner_disk = null;
            $fdrProfile->company_banner_img_path = null;
            $fdrProfile->save();
        }
    }

    /**
     * @throws ActionException
     */
    public static function execute(User $user): FounderProfile
    {
        if (strtolower($user->type) != strtolower(User::FOUNDER)) {
            throw new ActionException(__('You can only update company images for a founder user'));
        }

        $disk = config('filesystems.default');

        /**@var FounderProfile $fdrProfile */
        $fdrProfile = $user->fdrProfile;

        if (self::$removeLogo) self::removeLogo($fdrProfile);

        if (self::$uploadedLogo instanceof UploadedFile) {
            self::removeLogo($fdrProfile);
            $filepath = Storage::disk($disk)
                ->put('logos', self::$uploadedLogo, 'public');

            $fdrProfile->company_logo_path = $filepath;
            $fdrProfile->company_logo_disk = $disk;
            $fdrProfile->save();
        }

        if (self::$removeBanner) {
            Log::info('true');
            self::removeBanner($fdrProfile);
        }

        if (self::$uploadedBannerImg instanceof UploadedFile) {
            self::removeBanner($fdrProfile);
            $filepath = Storage::disk($disk)
                ->put('logos', self::$uploadedBannerImg, 'public');

            $fdrProfile->company_banner_img_path = $filepath;
            $fdrProfile->company_banner_disk = $disk;
            $fdrProfile->save();
        }

        $fdrProfile->refresh();
        return $fdrProfile;
    }
}

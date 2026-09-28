<?php

namespace App\Services;

use App\Models\User;
use BaconQrCode\Renderer\Color\Rgb;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\Fill;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use PragmaRX\Google2FA\Google2FA;

/**
 * Time-based one-time password (TOTP) two-factor authentication, compatible with
 * Google Authenticator, Authy, 1Password, etc.
 */
class TwoFactorService
{
    public function __construct(private readonly Google2FA $engine = new Google2FA) {}

    public function generateSecret(): string
    {
        return $this->engine->generateSecretKey(32);
    }

    public function verify(User $user, string $code): bool
    {
        $code = preg_replace('/\s+/', '', $code);

        if (! $user->two_factor_secret || ! preg_match('/^\d{6}$/', $code)) {
            return false;
        }

        // Window of 1 allows for slight clock drift between phone and server.
        return (bool) $this->engine->verifyKey($user->two_factor_secret, $code, 1);
    }

    /** Verifies a recovery code, consuming it on success. */
    public function verifyRecoveryCode(User $user, string $code): bool
    {
        $codes = $user->two_factor_recovery_codes ?? [];
        $code = strtoupper(trim($code));

        foreach ($codes as $index => $hashed) {
            if (Hash::check($code, $hashed)) {
                unset($codes[$index]);
                $user->forceFill(['two_factor_recovery_codes' => array_values($codes)])->save();

                return true;
            }
        }

        return false;
    }

    /** @return array<int, string> plain-text codes (shown once); hashes are what get stored */
    public function generateRecoveryCodes(User $user): array
    {
        $plain = collect(range(1, 8))
            ->map(fn () => strtoupper(Str::random(5).'-'.Str::random(5)))
            ->all();

        $user->forceFill([
            'two_factor_recovery_codes' => array_map(fn ($c) => Hash::make($c), $plain),
        ])->save();

        return $plain;
    }

    public function otpauthUrl(User $user, string $secret): string
    {
        return $this->engine->getQRCodeUrl(config('app.name'), $user->email, $secret);
    }

    /** Inline SVG QR code for the authenticator app to scan. */
    public function qrCodeSvg(string $otpauthUrl): string
    {
        $renderer = new ImageRenderer(
            new RendererStyle(200, 2, null, null, Fill::uniformColor(new Rgb(255, 255, 255), new Rgb(16, 24, 40))),
            new SvgImageBackEnd
        );

        $svg = (new Writer($renderer))->writeString($otpauthUrl);

        return trim(substr($svg, strpos($svg, "\n") + 1)); // drop the XML declaration
    }
}

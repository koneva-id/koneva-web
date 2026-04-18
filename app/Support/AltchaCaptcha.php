<?php

namespace App\Support;

use AltchaOrg\Altcha\Algorithm\Pbkdf2;
use AltchaOrg\Altcha\Altcha;
use AltchaOrg\Altcha\Challenge;
use AltchaOrg\Altcha\ChallengeParameters;
use AltchaOrg\Altcha\CreateChallengeOptions;
use AltchaOrg\Altcha\Payload;
use AltchaOrg\Altcha\Solution;
use AltchaOrg\Altcha\VerifySolutionOptions;

class AltchaCaptcha
{
    public static function enabled(): bool
    {
        return (bool) config('services.altcha.enabled', false)
            && self::secret() !== '';
    }

    /**
     * @return array<string, mixed>
     */
    public static function createChallenge(): array
    {
        $challenge = self::client()->createChallenge(new CreateChallengeOptions(
            algorithm: new Pbkdf2(),
            cost: (int) config('services.altcha.cost', 12000),
            expiresAt: time() + (int) config('services.altcha.ttl', 300),
        ));

        return $challenge->toArray();
    }

    public static function verify(?string $payloadBase64): bool
    {
        if (! self::enabled() || app()->environment('testing')) {
            return true;
        }

        if (! is_string($payloadBase64) || trim($payloadBase64) === '') {
            return false;
        }

        $json = base64_decode($payloadBase64, true);

        if (! is_string($json) || $json === '') {
            return false;
        }

        $parsed = json_decode($json, true);

        if (! is_array($parsed)) {
            return false;
        }

        $challengeData = $parsed['challenge'] ?? null;
        $solutionData = $parsed['solution'] ?? null;

        if (! is_array($challengeData) || ! is_array($solutionData)) {
            return false;
        }

        $parametersData = $challengeData['parameters'] ?? null;

        if (! is_array($parametersData)) {
            return false;
        }

        $counter = $solutionData['counter'] ?? null;
        $derivedKey = $solutionData['derivedKey'] ?? null;

        if (! is_int($counter) || ! is_string($derivedKey) || $derivedKey === '') {
            return false;
        }

        $parameters = ChallengeParameters::fromArray($parametersData);
        $challenge = new Challenge(
            parameters: $parameters,
            signature: is_string($challengeData['signature'] ?? null) ? $challengeData['signature'] : null,
        );

        $time = $solutionData['time'] ?? null;
        $solution = new Solution(
            counter: $counter,
            derivedKey: $derivedKey,
            time: is_numeric($time) ? (float) $time : null,
        );

        $payload = new Payload($challenge, $solution);
        $result = self::client()->verifySolution(new VerifySolutionOptions(
            payload: $payload,
            algorithm: new Pbkdf2(),
        ));

        return (bool) $result->verified;
    }

    private static function client(): Altcha
    {
        $keySecret = (string) config('services.altcha.key_secret', '');

        return new Altcha(
            hmacSignatureSecret: self::secret(),
            hmacKeySignatureSecret: $keySecret !== '' ? $keySecret : null,
        );
    }

    private static function secret(): string
    {
        return (string) config('services.altcha.secret', '');
    }
}

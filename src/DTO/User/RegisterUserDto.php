<?php
declare(strict_types=1);

namespace App\DTO\User;

use Symfony\Component\Validator\Constraints as Assert;

class RegisterUserDto
{
    #[Assert\NotBlank]
    #[Assert\Email]
    #[Assert\Length(max: 255)]
    public string $email;

    #[Assert\NotBlank]
    #[Assert\Length(min: 8)]
    public string $password;

    #[Assert\NotBlank]
    #[Assert\Timezone]
    public ?string $timezone = null;

    #[Assert\NotBlank]
    #[Assert\Language]
    public ?string $locale = null;

    public function __construct(
        string $email,
        string $password,
        string $timezone = 'UTC',
        string $locale = 'en'
    ) {
        $this->email = $email;
        $this->password = $password;
        $this->timezone = $timezone;
        $this->locale = $locale;
    }
}

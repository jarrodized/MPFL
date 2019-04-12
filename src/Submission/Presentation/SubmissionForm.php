<?php declare(strict_types=1);

namespace SocialNews\Submission\Presentation;

use SocialNews\Framework\Csrf\StoredTokenValidator;
use SocialNews\Framework\Csrf\Token;
use SocialNews\Submission\Application\SubmitLink;
use SocialNews\Framework\Rbac\AuthenticatedUser;

final class SubmissionForm
{
    private $storedTokenValidator;
    private $token;
    private $title;
    private $url;

    public function __construct(
        StoredTokenValidator $storedTokenValidator,
        string $token,
        string $title,
        string $url
    ) {
        $this->storedTokenValidator = $storedTokenValidator;
        $this->token = $token;
        $this->title = $title;
        $this->url = $url;
    }

    public function hasValidationErrors(): bool
    {
        return (count($this->getValidationErrors()) > 0);
    }

    /**
     * @return string[]
     */
    public function getValidationErrors(): array
    {
        $errors = [];
        if (!$this->storedTokenValidator->validate(
            'submission',
            new Token($this->token)
        )) {
            $errors[] = 'Invalid token';
        }
        if (strlen($this->title) < 1 || strlen($this->title) > 200) {
            $errors[] = 'Title must be between 1 and 200 characters';
        }
        if (strlen($this->url) < 1 || strlen($this->url) > 200) {
            $errors[] = 'URL must be between 1 and 200 characters';
        }
        return $errors;
    }

    public function toCommand(AuthenticatedUser $author): SubmitLink
    {
        return new SubmitLink($author->getId(), $this->url, $this->title);
    }
}
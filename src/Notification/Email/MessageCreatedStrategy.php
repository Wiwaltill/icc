<?php

namespace App\Notification\Email;

use App\Entity\MessageScope;
use App\Event\MessageCreatedEvent;
use App\Message\MessageRecipientResolver;
use App\Repository\MessageRepositoryInterface;
use SchulIT\CommonBundle\Helper\DateHelper;
use Symfony\Contracts\Translation\TranslatorInterface;

class MessageCreatedStrategy implements EmailStrategyInterface, PostEmailSendActionInterface {

    private $translator;
    private $messageRepository;
    private $dateHelper;
    private $recipientResolver;

    public function __construct(TranslatorInterface $translator, MessageRecipientResolver $recipientResolver, MessageRepositoryInterface $messageRepository, DateHelper $dateHelper) {
        $this->translator = $translator;
        $this->messageRepository = $messageRepository;
        $this->dateHelper = $dateHelper;
        $this->recipientResolver = $recipientResolver;
    }

    /**
     * @inheritDoc
     */
    public function isEnabled(): bool {
        return true;
    }

    /**
     * @param MessageCreatedEvent $objective
     * @return string|null
     */
    public function getReplyTo($objective): ?string {
        return $objective->getMessage()->getCreatedBy()->getEmail();
    }

    /**
     * @param MessageCreatedEvent $objective
     * @return array
     */
    public function getRecipients($objective): array {
        return $this->recipientResolver->resolveRecipients($objective->getMessage());
    }

    /**
     * @param MessageCreatedEvent $objective
     * @return string
     */
    public function getSubject($objective): string {
        return $this->translator->trans('message.create.title', ['%title%' => $objective->getMessage()->getTitle()], 'email');
    }

    /**
     * @param MessageCreatedEvent $objective
     * @return string
     */
    public function getSender($objective): string {
        $creator = $objective->getMessage()->getCreatedBy();

        return sprintf('%s %s', $creator->getFirstname(), $creator->getLastname());
    }

    /**
     * @inheritDoc
     */
    public function getTemplate(): string {
        return 'email/message.html.twig';
    }

    /**
     * @param MessageCreatedEvent $objective
     */
    public function onNotificationSent($objective): void {
        $objective->getMessage()->setIsEmailNotificationSent(true);
        $this->messageRepository->persist($objective->getMessage());
    }

    /**
     * @inheritDoc
     */
    public function supports($objective): bool {
        return $objective instanceof MessageCreatedEvent
            && $objective->getMessage()->getScope()->equals(MessageScope::Messages())
            && $objective->getMessage()->isEmailNotificationSent() === false
            && $objective->getMessage()->getStartDate() <= $this->dateHelper->getToday();
    }
}
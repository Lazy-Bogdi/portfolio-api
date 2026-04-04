<?php

namespace App\MessageHandler;

use App\Message\MercureUpdateMessage;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class MercureUpdateHandler
{
    public function __construct(
        private readonly HubInterface $hub,
        private readonly EntityManagerInterface $em,
        private readonly SerializerInterface $serializer,
    ) {
    }

    public function __invoke(MercureUpdateMessage $message): void
    {
        if ('deleted' === $message->action) {
            $data = json_encode([
                'action' => 'deleted',
                'id' => $message->entityId,
            ]);
        } else {
            /** @var object|null $entity */
            $entity = $this->em->find($message->entityClass, $message->entityId);
            if (null === $entity) {
                return;
            }

            $context = SerializationContext::create()->setGroups(['detail']);
            $data = $this->serializer->serialize([
                'action' => $message->action,
                'data' => $entity,
            ], 'json', $context);
        }

        $topic = $this->buildTopic($message);
        $this->hub->publish(new Update($topic, $data));
    }

    private function buildTopic(MercureUpdateMessage $message): string
    {
        $shortClass = lcfirst((new \ReflectionClass($message->entityClass))->getShortName());

        return sprintf('/api/%ss/%d', $shortClass, $message->entityId);
    }
}

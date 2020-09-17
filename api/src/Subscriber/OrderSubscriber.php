<?php

namespace App\Subscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Entity\Order;
use Conduction\CommonGroundBundle\Service\CommonGroundService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Serializer\SerializerInterface;

//use App\Entity\Request as CCRequest;

class OrderSubscriber implements EventSubscriberInterface
{
    private $params;
    private $em;
    private $serializer;
    private $nlxLogService;
    private $commonGroundService;

    public function __construct(ParameterBagInterface $params, EntityManagerInterface $em, SerializerInterface $serializer, CommonGroundService $commonGroundService)
    {
        $this->params = $params;
        $this->em = $em;
        $this->serializer = $serializer;
        $this->commonGroundService = $commonGroundService;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW => ['newOrder', EventPriorities::PRE_VALIDATE],
        ];
    }

    public function newOrder(ViewEvent $event)
    {
        $result = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();
        $route = $event->getRequest()->attributes->get('_route');

        if (!$result instanceof Order || $route != 'api_orders_post_collection') {
            //var_dump('a');
            return;
        }

        if (!$result->getReference()) {

            $organization = $this->commonGroundService->getResource($result->getOrganization());
            if (array_key_exists('shortcode', $organization) && $organization['shortcode'] != null) {
                $shortcode = $organization['shortcode'];
            } else {
                $shortcode = $organization['name'];
            }

            // Lets get a reference id
            $referenceId = $this->em->getRepository('App\Entity\Order')->getLastReferenceId($organization['@id']);

            // Turn that into a reference and check for double references
            $double = true;
            while ($double) {
                $referenceId++;
                $reference = $shortcode.'-'.date('Y').'-'.$referenceId;
                $double = $this->em->getRepository('App\Entity\Order')->findOneBy(['reference' => $reference]);
            }

            $result->setReference($shortcode.'-'.date('Y').'-'.$referenceId);
            $result->setReferenceId($referenceId);
        }

        return $result;
    }
}

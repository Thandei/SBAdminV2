<?php

namespace App\Subscribers\Locale;


use App\Kernel;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use function Symfony\Component\String\u;

class RedirectToPreferredSubscriberLocale implements EventSubscriberInterface
{
private array $locales;
private string $defaultLocale;


    public function __construct(

            private UrlGeneratorInterface $urlGenerator,
            string $locales,
            ?string $defaultLocale = null) {


            $this->locales = explode("|", trim($locales));

            if(empty($this->locales)){
                throw new \UnexpectedValueException("the list of supported locales must not be empty!");
            }

            $this->defaultLocale = $defaultLocale ?: $this->locales[0];

            if(!\in_array($this->defaultLocale, $this->locales, true)){
                throw new \UnexpectedValueException(sprintf("the default locale must be one of ..."));
            }

            array_unshift($this->locales, $this->defaultLocale);
            $this->locales = array_unique($this->locales);
    }

    #[ArrayShape([KernelEvents::REQUEST => "string"])]
    public static function getSubscribedEvents(): array
    {
        return [

            KernelEvents::REQUEST => "onKernelRequest",
        ];
    }


    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();

        if(!$event->isMainRequest() || "/" !== $request->getPathInfo()){
            return;
        }


        $referrer = $request->headers->get("referer");
        if (null !== $referrer && u($referrer)->ignoreCase()->startsWith($request->getSchemeAndHttpHost())){
             return;
        }

        $preferredLanguage = $request->getPreferredLanguage($this->locales);

        if ($preferredLanguage !== $this->defaultLocale){
            $response = new RedirectResponse($this->urlGenerator->generate("default_index", ["_locale" => $preferredLanguage]));
            $event->setResponse($response);
        }
    }
}
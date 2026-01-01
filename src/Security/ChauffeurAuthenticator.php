<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\SecurityRequestAttributes;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use App\Entity\Chauffeur;


class ChauffeurAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_login';

    public function __construct(private UrlGeneratorInterface $urlGenerator)
    {
    }

   public function authenticate(Request $request): Passport
{
    $email = $request->request->get('email'); // <- ici

    $request->getSession()->set(SecurityRequestAttributes::LAST_USERNAME, $email);

    return new Passport(
        new UserBadge($email),
        new PasswordCredentials($request->request->get('password')), // <- icis
        [
            new CsrfTokenBadge('authenticate', $request->request->get('_csrf_token')), // <- ici
            new RememberMeBadge(),
        ]
    );
}


    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        // Redirection vers la page cible si elle existe
      //  if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
        //    return new RedirectResponse($targetPath);
        //}

        // Récupère l'utilisateur connecté
        $user = $token->getUser();

        // Si c'est bien un Chauffeur, redirige vers sa page show
        if ($user instanceof Chauffeur) {
            return new RedirectResponse(
                $this->urlGenerator->generate('app_chauffeur_show', [
                    'id' => $user->getId(),
                ])
            );
        }

        // Sinon, redirection par défaut
        return new RedirectResponse($this->urlGenerator->generate('app_chauffeur_index'));
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}

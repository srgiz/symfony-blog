<?php
namespace App\Entity\User;

use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface as CoreUserInterface;

/**
 * Represents the interface that all user classes must implement.
 *
 * This interface is useful because the authentication layer can deal with
 * the object through its lifecycle, using the object to get the hashed
 * password (for checking against a submitted password), assigning roles
 * and so on.
 *
 * Regardless of how your users are loaded or where they come from (a database,
 * configuration, web service, etc.), you will have a class that implements
 * this interface. Objects that implement this interface are created and
 * loaded by different objects that implement UserProviderInterface.
 *
 * @see UserProviderInterface
 *
 * @method string getUserIdentifier() returns the identifier for this user (e.g. its username or e-mail address)
 */
interface UserInterface extends PasswordAuthenticatedUserInterface, CoreUserInterface
{
}

<?php

declare(strict_types=1);

namespace TorrentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;
use FOS\UserBundle\Model\User as FOSUser;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(
 *      name="m_user",
 *      options={"comment"="Holds users"}
 * )
 * @ORM\Entity(repositoryClass="TorrentBundle\Repository\UserRepository")
 * @ORM\HasLifecycleCallbacks
 * @UniqueEntity(fields="email", message="It seems you are already registered!")
 */
class User extends FOSUser implements UserInterface
{
    use EntityTrait;

    /**
     * @var int
     * @ORM\Column(type="integer", options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @var int
     * @ORM\Column(name="current_message", type="integer", nullable=false, options={"comment"="How many app persistent message this user has seen"})
     * @Assert\GreaterThanOrEqual (
     *      value = 0,
     *      message = "The user current message is too low! (min: {{ compared_value }})."
     * )
     */
    protected $currentMessage = 0;

    /**
     * @var string
     */
    protected $username;

    /**
     * @var string
     */
    protected $usernameCanonical;

    /**
     * @var string
     */
    protected $email;

    /**
     * @var string
     */
    protected $emailCanonical;

    /**
     * @var bool
     */
    protected $enabled;

    /**
     * @var string
     */
    protected $salt;

    /**
     * Encrypted password. Must be persisted.
     *
     * @var string
     */
    protected $password;

    /**
     * Plain password. Used for model validation. Must not be persisted.
     *
     * @var string
     *
     * @Assert\Length(
     *      min = 10,
     *      minMessage = "Please pick a password of at least {{ limit }} characters. That might seems a lot, but Moustache values security.",
     *      groups={"default", "signup"}
     * )
     */
    protected $plainPassword;

    /**
     * @var \DateTime
     */
    protected $lastLogin;

    /**
     * @var bool
     */
    protected $firstTime;

    /**
     * Random string sent to the user email address in order to verify it.
     *
     * @var string
     */
    protected $confirmationToken;

    /**
     * @var \DateTime
     */
    protected $passwordRequestedAt;

    /**
     * @var Collection
     */
    protected $groups;

    /**
     * @var bool
     */
    protected $locked;

    /**
     * @var bool
     */
    protected $expired;

    /**
     * @var \DateTime
     */
    protected $expiresAt;

    /**
     * @var array
     */
    protected $roles;

    /**
     * @var bool
     */
    protected $credentialsExpired;

    /**
     * @var \DateTime
     */
    protected $credentialsExpireAt;

    /**
     * @var TorrentInterface[]
     * @ORM\OneToMany(targetEntity="Torrent", mappedBy="user", cascade={"remove"}, fetch="EAGER")
     */
    protected $torrents;

    /**
     * @var string[]
     */
    protected $torrentHashes = null;

    /**
     * {@inheritdoc}
     */
    public function __toString(): string
    {
        return $this->username;
    }

    /**
     * {@inheritdoc}
     */
    public function isNew(): bool
    {
        return 0 === $this->currentMessage;
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrentMessage(): int
    {
        return $this->currentMessage;
    }

    /**
     * {@inheritdoc}
     */
    public function setCurrentMessage(int $currentMessage)
    {
        $this->currentMessage = $currentMessage;
    }

    /**
     * {@inheritdoc}
     */
    public function getTorrents(): PersistentCollection
    {
        return $this->torrents;
    }

    /**
     * {@inheritdoc}
     */
    public function getTorrentHashes(): array
    {
        if (null === $this->torrentHashes) {
            $this->torrentHashes = array_map(function ($torrent) {
                return $torrent->getHash();
            }, $this->torrents->toArray());
        }

        return $this->torrentHashes;
    }

    /**
     * {@inheritdoc}
     */
    public function setTorrents(array $torrents)
    {
        $this->torrents = $torrents;
    }
}

<?php

namespace App\Repository\Module\Forum;

use App\Entity\Module\Forum\Comment;
use App\Entity\Module\Forum\Forum;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * CommentRepository.
 *
 * @extends ServiceEntityRepository<Comment>
 *
 * @method Comment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Comment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Comment[]    findAll()
 * @method Comment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 *
 * @author Sébastien FOURNIER <contact@sebastien-fournier.com>
 */
class CommentRepository extends ServiceEntityRepository
{
    /**
     * CommentRepository constructor.
     */
    public function __construct(private readonly ManagerRegistry $registry)
    {
        parent::__construct($this->registry, Comment::class);
    }

    /**
     * Find by Forum.
     *
     * @return Comment[]
     */
    public function findByForum(Forum $forum)
    {
        return $this->createQueryBuilder('c')
            ->leftJoin('c.forum', 'f')
            ->andWhere('c.forum = :forum')
            ->andWhere('c.active = :active')
            ->andWhere('c.parent IS NULL')
            ->setParameter('active', true)
            ->setParameter('forum', $forum)
            ->addSelect('f')
            ->orderBy('c.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}

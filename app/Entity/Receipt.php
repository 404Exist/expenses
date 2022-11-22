<?php

declare(strict_types=1);

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\Table;

#[Entity, Table('receipts')]
class Receipt
{
    #[Id, Column(options: ['unsigned' => true]), GeneratedValue]
    private int $id;

    #[Column]
    private string $file_name;

    #[Column]
    private DateTime $created_at;

    #[Column]
    private DateTime $updated_at;

    #[ManyToOne(inversedBy: 'receipts')]
    private Transaction $transaction;

    public function getId(): int
    {
        return $this->id;
    }

    public function getFileName(): string
    {
        return $this->file_name;
    }

    public function setFileName($file_name): Receipt
    {
        $this->file_name = $file_name;

        return $this;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->created_at;
    }

    public function setCreatedAt(DateTime $created_at): Receipt
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUpdatedAt(): DateTime
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(DateTime $updated_at): Receipt
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    public function getTransaction(): Transaction
    {
        return $this->transaction;
    }

    public function setTransaction(Transaction $transaction): Receipt
    {
        $transaction->addReceipt($this);

        $this->transaction = $transaction;

        return $this;
    }
}

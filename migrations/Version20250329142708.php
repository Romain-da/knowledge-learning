<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250329142708 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE achat_lecon (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, lecon_id INT NOT NULL, date_achat DATETIME NOT NULL, montant DOUBLE PRECISION NOT NULL, INDEX IDX_8CCC59D7A76ED395 (user_id), INDEX IDX_8CCC59D7EC1308A5 (lecon_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE achat_lecon ADD CONSTRAINT FK_8CCC59D7A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE achat_lecon ADD CONSTRAINT FK_8CCC59D7EC1308A5 FOREIGN KEY (lecon_id) REFERENCES lecon (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE achat_lecon DROP FOREIGN KEY FK_8CCC59D7A76ED395');
        $this->addSql('ALTER TABLE achat_lecon DROP FOREIGN KEY FK_8CCC59D7EC1308A5');
        $this->addSql('DROP TABLE achat_lecon');
    }
}

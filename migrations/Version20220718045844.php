<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220718045844 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'init database';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE file (id INT AUTO_INCREMENT NOT NULL, job_id INT DEFAULT NULL, file_path VARCHAR(255) DEFAULT NULL, INDEX IDX_8C9F3610BE04EA9 (job_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE job (id INT AUTO_INCREMENT NOT NULL, repository_name VARCHAR(255) NOT NULL, commit_name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE rule (id INT AUTO_INCREMENT NOT NULL, job_id INT DEFAULT NULL, trigger_name VARCHAR(255) NOT NULL, trigger_value VARCHAR(255) DEFAULT NULL, action_name VARCHAR(255) NOT NULL, action_value VARCHAR(255) DEFAULT NULL, INDEX IDX_46D8ACCCBE04EA9 (job_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE file ADD CONSTRAINT FK_8C9F3610BE04EA9 FOREIGN KEY (job_id) REFERENCES job (id)');
        $this->addSql('ALTER TABLE rule ADD CONSTRAINT FK_46D8ACCCBE04EA9 FOREIGN KEY (job_id) REFERENCES job (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE file DROP FOREIGN KEY FK_8C9F3610BE04EA9');
        $this->addSql('ALTER TABLE rule DROP FOREIGN KEY FK_46D8ACCCBE04EA9');
        $this->addSql('DROP TABLE file');
        $this->addSql('DROP TABLE job');
        $this->addSql('DROP TABLE rule');
    }
}

<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250203090303 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE settings_payments DROP FOREIGN KEY FK_43B5AF365FB14BA7');
        $this->addSql('ALTER TABLE settings_payments DROP FOREIGN KEY FK_43B5AF36D2EECC3F');
        $this->addSql('DROP TABLE settings_payments');
        $this->addSql('ALTER TABLE evaluation DROP FOREIGN KEY fk_evaluation_user');
        $this->addSql('ALTER TABLE evaluation CHANGE author_id author_id INT DEFAULT NULL, CHANGE mini mini DOUBLE PRECISION DEFAULT \'0\' NOT NULL, CHANGE maxi maxi DOUBLE PRECISION DEFAULT \'20\' NOT NULL');
        $this->addSql('DROP INDEX fk_evaluation_user ON evaluation');
        $this->addSql('CREATE INDEX IDX_1323A575F675F31B ON evaluation (author_id)');
        $this->addSql('ALTER TABLE evaluation ADD CONSTRAINT fk_evaluation_user FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE payment CHANGE subscription subscription TINYINT(1) DEFAULT 0 NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_6D28840D77153098 ON payment (code)');
        $this->addSql('ALTER TABLE school_year CHANGE rate rate INT NOT NULL');
        $this->addSql('ALTER TABLE student DROP FOREIGN KEY FK_B723AF338447EB58');
        $this->addSql('DROP INDEX fk_b723af338447eb58 ON student');
        $this->addSql('CREATE INDEX IDX_B723AF338447EB58 ON student (entry_class_id)');
        $this->addSql('ALTER TABLE student ADD CONSTRAINT FK_B723AF338447EB58 FOREIGN KEY (entry_class_id) REFERENCES class_room (id)');
        $this->addSql('ALTER TABLE user ADD roles JSON NOT NULL COMMENT \'(DC2Type:json)\', DROP github_id, DROP github_access_token, DROP facebook_id, DROP facebook_access_token, DROP google_id, DROP google_access_token, DROP linkedin_id, DROP linkedin_access_token, DROP twitter_id, DROP twitter_access_token, DROP yahoo_id, DROP yahoo_access_token, CHANGE security_question security_question VARCHAR(255) DEFAULT NULL, CHANGE security_answer security_answer VARCHAR(255) DEFAULT \'bethesda\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE settings_payments (id INT AUTO_INCREMENT NOT NULL, level_id INT NOT NULL, school_year_id INT NOT NULL, dead_line DATE NOT NULL, amount INT NOT NULL, reason VARCHAR(12) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, INDEX IDX_43B5AF36D2EECC3F (school_year_id), INDEX IDX_43B5AF365FB14BA7 (level_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE settings_payments ADD CONSTRAINT FK_43B5AF365FB14BA7 FOREIGN KEY (level_id) REFERENCES level (id)');
        $this->addSql('ALTER TABLE settings_payments ADD CONSTRAINT FK_43B5AF36D2EECC3F FOREIGN KEY (school_year_id) REFERENCES school_year (id)');
        $this->addSql('ALTER TABLE evaluation DROP FOREIGN KEY FK_1323A575F675F31B');
        $this->addSql('ALTER TABLE evaluation CHANGE author_id author_id INT DEFAULT 120, CHANGE mini mini DOUBLE PRECISION DEFAULT \'0\', CHANGE maxi maxi DOUBLE PRECISION DEFAULT \'20\'');
        $this->addSql('DROP INDEX idx_1323a575f675f31b ON evaluation');
        $this->addSql('CREATE INDEX fk_evaluation_user ON evaluation (author_id)');
        $this->addSql('ALTER TABLE evaluation ADD CONSTRAINT FK_1323A575F675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('DROP INDEX UNIQ_6D28840D77153098 ON payment');
        $this->addSql('ALTER TABLE payment CHANGE subscription subscription TINYINT(1) DEFAULT 0');
        $this->addSql('ALTER TABLE school_year CHANGE rate rate INT DEFAULT NULL');
        $this->addSql('ALTER TABLE student DROP FOREIGN KEY FK_B723AF338447EB58');
        $this->addSql('DROP INDEX idx_b723af338447eb58 ON student');
        $this->addSql('CREATE INDEX FK_B723AF338447EB58 ON student (entry_class_id)');
        $this->addSql('ALTER TABLE student ADD CONSTRAINT FK_B723AF338447EB58 FOREIGN KEY (entry_class_id) REFERENCES class_room (id)');
        $this->addSql('ALTER TABLE user ADD github_id VARCHAR(255) DEFAULT NULL, ADD github_access_token VARCHAR(255) DEFAULT NULL, ADD facebook_id VARCHAR(255) DEFAULT NULL, ADD facebook_access_token VARCHAR(255) DEFAULT NULL, ADD google_id VARCHAR(255) DEFAULT NULL, ADD google_access_token VARCHAR(255) DEFAULT NULL, ADD linkedin_id VARCHAR(255) DEFAULT NULL, ADD linkedin_access_token VARCHAR(255) DEFAULT NULL, ADD twitter_id VARCHAR(255) DEFAULT NULL, ADD twitter_access_token VARCHAR(255) DEFAULT NULL, ADD yahoo_id VARCHAR(255) DEFAULT NULL, ADD yahoo_access_token VARCHAR(255) DEFAULT NULL, DROP roles, CHANGE security_question security_question VARCHAR(200) DEFAULT NULL, CHANGE security_answer security_answer VARCHAR(200) DEFAULT NULL');
    }
}

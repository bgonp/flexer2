<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200614162403 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE assignment (id CHAR(36) NOT NULL, customer_id CHAR(36) DEFAULT NULL, course_id CHAR(36) DEFAULT NULL, position_id CHAR(36) DEFAULT NULL, first_session_id CHAR(36) DEFAULT NULL, last_session_id CHAR(36) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, notes VARCHAR(255) DEFAULT NULL, INDEX IDX_30C544BA9395C3F3 (customer_id), INDEX IDX_30C544BA591CC992 (course_id), INDEX IDX_30C544BADD842E46 (position_id), INDEX IDX_30C544BAF0A78DD8 (first_session_id), INDEX IDX_30C544BA884B8635 (last_session_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE period (id CHAR(36) NOT NULL, season_id CHAR(36) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, name VARCHAR(255) NOT NULL, init_date DATE DEFAULT NULL, finish_date DATE DEFAULT NULL, holidays LONGTEXT NOT NULL COMMENT \'(DC2Type:simple_array)\', INDEX IDX_C5B81ECE4EC001D1 (season_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE zone (id CHAR(36) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE discipline (id CHAR(36) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, url VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE place (id CHAR(36) NOT NULL, zone_id CHAR(36) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, url VARCHAR(255) DEFAULT NULL, INDEX IDX_741D53CD9F2C3FAB (zone_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE session (id CHAR(36) NOT NULL, course_id CHAR(36) DEFAULT NULL, period_id CHAR(36) DEFAULT NULL, place_id CHAR(36) DEFAULT NULL, discipline_id CHAR(36) DEFAULT NULL, level_id CHAR(36) DEFAULT NULL, age_id CHAR(36) DEFAULT NULL, retrieval_session_id CHAR(36) DEFAULT NULL, listing_id CHAR(36) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, day DATE DEFAULT NULL, time TIME DEFAULT NULL, duration SMALLINT DEFAULT NULL, is_cancelled TINYINT(1) DEFAULT \'0\' NOT NULL, INDEX IDX_D044D5D4591CC992 (course_id), INDEX IDX_D044D5D4EC8B7ADE (period_id), INDEX IDX_D044D5D4DA6A219 (place_id), INDEX IDX_D044D5D4A5522701 (discipline_id), INDEX IDX_D044D5D45FB14BA7 (level_id), INDEX IDX_D044D5D4CC80CD12 (age_id), INDEX IDX_D044D5D43D77B39E (retrieval_session_id), INDEX IDX_D044D5D4D4619D1A (listing_id), UNIQUE INDEX UNIQ_D044D5D4591CC992E5A029906F949845 (course_id, day, time), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE course (id CHAR(36) NOT NULL, school_id CHAR(36) DEFAULT NULL, place_id CHAR(36) DEFAULT NULL, discipline_id CHAR(36) DEFAULT NULL, level_id CHAR(36) DEFAULT NULL, age_id CHAR(36) DEFAULT NULL, listing_id CHAR(36) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, is_active TINYINT(1) NOT NULL, week_of_month LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:simple_array)\', day_of_week LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:simple_array)\', time TIME DEFAULT NULL, duration SMALLINT DEFAULT NULL, INDEX IDX_169E6FB9C32A47EE (school_id), INDEX IDX_169E6FB9DA6A219 (place_id), INDEX IDX_169E6FB9A5522701 (discipline_id), INDEX IDX_169E6FB95FB14BA7 (level_id), INDEX IDX_169E6FB9CC80CD12 (age_id), INDEX IDX_169E6FB9D4619D1A (listing_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE school (id CHAR(36) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, url VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE rate (id CHAR(36) NOT NULL, school_id CHAR(36) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, name VARCHAR(255) NOT NULL, amount NUMERIC(6, 2) NOT NULL, available TINYINT(1) DEFAULT \'0\' NOT NULL, spreadable TINYINT(1) DEFAULT \'1\' NOT NULL, customers_count SMALLINT DEFAULT 1 NOT NULL, periods_count SMALLINT DEFAULT 1 NOT NULL, courses_count SMALLINT DEFAULT 1 NOT NULL, INDEX IDX_DFEC3F39C32A47EE (school_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE listing (id CHAR(36) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id CHAR(36) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:simple_array)\', UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE attendance (id CHAR(36) NOT NULL, customer_id CHAR(36) DEFAULT NULL, session_id CHAR(36) DEFAULT NULL, position_id CHAR(36) DEFAULT NULL, payment_id CHAR(36) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, is_used TINYINT(1) DEFAULT \'0\' NOT NULL, will_be_used TINYINT(1) DEFAULT \'1\' NOT NULL, is_paid_here TINYINT(1) DEFAULT \'0\' NOT NULL, notes VARCHAR(255) DEFAULT NULL, INDEX IDX_6DE30D919395C3F3 (customer_id), INDEX IDX_6DE30D91613FECDF (session_id), INDEX IDX_6DE30D91DD842E46 (position_id), INDEX IDX_6DE30D914C3A3BB (payment_id), UNIQUE INDEX UNIQ_6DE30D919395C3F3613FECDF (customer_id, session_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE payment (id CHAR(36) NOT NULL, rate_id CHAR(36) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, amount NUMERIC(6, 2) DEFAULT \'0\' NOT NULL, is_transfer TINYINT(1) DEFAULT \'0\' NOT NULL, document VARCHAR(255) DEFAULT NULL, notes VARCHAR(255) DEFAULT NULL, INDEX IDX_6D28840DBC999F9F (rate_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE family (id CHAR(36) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, notes VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE level (id CHAR(36) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, url VARCHAR(255) DEFAULT NULL, difficulty SMALLINT DEFAULT 1 NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE position (id CHAR(36) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, name VARCHAR(255) NOT NULL, is_staff TINYINT(1) DEFAULT \'0\' NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE customer (id CHAR(36) NOT NULL, family_id CHAR(36) DEFAULT NULL, user_id CHAR(36) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, name VARCHAR(255) NOT NULL, surname VARCHAR(255) DEFAULT NULL, birthdate DATE DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, phone VARCHAR(255) DEFAULT NULL, notes VARCHAR(255) DEFAULT NULL, staff INT NOT NULL, INDEX IDX_81398E09C35E566A (family_id), INDEX IDX_81398E09A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE season (id CHAR(36) NOT NULL, school_id CHAR(36) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, init_date DATE DEFAULT NULL, finish_date DATE DEFAULT NULL, INDEX IDX_F0E45BA9C32A47EE (school_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE age (id CHAR(36) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, url VARCHAR(255) DEFAULT NULL, min_age SMALLINT DEFAULT NULL, max_age SMALLINT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE assignment ADD CONSTRAINT FK_30C544BA9395C3F3 FOREIGN KEY (customer_id) REFERENCES customer (id)');
        $this->addSql('ALTER TABLE assignment ADD CONSTRAINT FK_30C544BA591CC992 FOREIGN KEY (course_id) REFERENCES course (id)');
        $this->addSql('ALTER TABLE assignment ADD CONSTRAINT FK_30C544BADD842E46 FOREIGN KEY (position_id) REFERENCES position (id)');
        $this->addSql('ALTER TABLE assignment ADD CONSTRAINT FK_30C544BAF0A78DD8 FOREIGN KEY (first_session_id) REFERENCES session (id)');
        $this->addSql('ALTER TABLE assignment ADD CONSTRAINT FK_30C544BA884B8635 FOREIGN KEY (last_session_id) REFERENCES session (id)');
        $this->addSql('ALTER TABLE period ADD CONSTRAINT FK_C5B81ECE4EC001D1 FOREIGN KEY (season_id) REFERENCES season (id)');
        $this->addSql('ALTER TABLE place ADD CONSTRAINT FK_741D53CD9F2C3FAB FOREIGN KEY (zone_id) REFERENCES zone (id)');
        $this->addSql('ALTER TABLE session ADD CONSTRAINT FK_D044D5D4591CC992 FOREIGN KEY (course_id) REFERENCES course (id)');
        $this->addSql('ALTER TABLE session ADD CONSTRAINT FK_D044D5D4EC8B7ADE FOREIGN KEY (period_id) REFERENCES period (id)');
        $this->addSql('ALTER TABLE session ADD CONSTRAINT FK_D044D5D4DA6A219 FOREIGN KEY (place_id) REFERENCES place (id)');
        $this->addSql('ALTER TABLE session ADD CONSTRAINT FK_D044D5D4A5522701 FOREIGN KEY (discipline_id) REFERENCES discipline (id)');
        $this->addSql('ALTER TABLE session ADD CONSTRAINT FK_D044D5D45FB14BA7 FOREIGN KEY (level_id) REFERENCES level (id)');
        $this->addSql('ALTER TABLE session ADD CONSTRAINT FK_D044D5D4CC80CD12 FOREIGN KEY (age_id) REFERENCES age (id)');
        $this->addSql('ALTER TABLE session ADD CONSTRAINT FK_D044D5D43D77B39E FOREIGN KEY (retrieval_session_id) REFERENCES session (id)');
        $this->addSql('ALTER TABLE session ADD CONSTRAINT FK_D044D5D4D4619D1A FOREIGN KEY (listing_id) REFERENCES listing (id)');
        $this->addSql('ALTER TABLE course ADD CONSTRAINT FK_169E6FB9C32A47EE FOREIGN KEY (school_id) REFERENCES school (id)');
        $this->addSql('ALTER TABLE course ADD CONSTRAINT FK_169E6FB9DA6A219 FOREIGN KEY (place_id) REFERENCES place (id)');
        $this->addSql('ALTER TABLE course ADD CONSTRAINT FK_169E6FB9A5522701 FOREIGN KEY (discipline_id) REFERENCES discipline (id)');
        $this->addSql('ALTER TABLE course ADD CONSTRAINT FK_169E6FB95FB14BA7 FOREIGN KEY (level_id) REFERENCES level (id)');
        $this->addSql('ALTER TABLE course ADD CONSTRAINT FK_169E6FB9CC80CD12 FOREIGN KEY (age_id) REFERENCES age (id)');
        $this->addSql('ALTER TABLE course ADD CONSTRAINT FK_169E6FB9D4619D1A FOREIGN KEY (listing_id) REFERENCES listing (id)');
        $this->addSql('ALTER TABLE rate ADD CONSTRAINT FK_DFEC3F39C32A47EE FOREIGN KEY (school_id) REFERENCES school (id)');
        $this->addSql('ALTER TABLE attendance ADD CONSTRAINT FK_6DE30D919395C3F3 FOREIGN KEY (customer_id) REFERENCES customer (id)');
        $this->addSql('ALTER TABLE attendance ADD CONSTRAINT FK_6DE30D91613FECDF FOREIGN KEY (session_id) REFERENCES session (id)');
        $this->addSql('ALTER TABLE attendance ADD CONSTRAINT FK_6DE30D91DD842E46 FOREIGN KEY (position_id) REFERENCES position (id)');
        $this->addSql('ALTER TABLE attendance ADD CONSTRAINT FK_6DE30D914C3A3BB FOREIGN KEY (payment_id) REFERENCES payment (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE payment ADD CONSTRAINT FK_6D28840DBC999F9F FOREIGN KEY (rate_id) REFERENCES rate (id)');
        $this->addSql('ALTER TABLE customer ADD CONSTRAINT FK_81398E09C35E566A FOREIGN KEY (family_id) REFERENCES family (id)');
        $this->addSql('ALTER TABLE customer ADD CONSTRAINT FK_81398E09A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE season ADD CONSTRAINT FK_F0E45BA9C32A47EE FOREIGN KEY (school_id) REFERENCES school (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE session DROP FOREIGN KEY FK_D044D5D4EC8B7ADE');
        $this->addSql('ALTER TABLE place DROP FOREIGN KEY FK_741D53CD9F2C3FAB');
        $this->addSql('ALTER TABLE session DROP FOREIGN KEY FK_D044D5D4A5522701');
        $this->addSql('ALTER TABLE course DROP FOREIGN KEY FK_169E6FB9A5522701');
        $this->addSql('ALTER TABLE session DROP FOREIGN KEY FK_D044D5D4DA6A219');
        $this->addSql('ALTER TABLE course DROP FOREIGN KEY FK_169E6FB9DA6A219');
        $this->addSql('ALTER TABLE assignment DROP FOREIGN KEY FK_30C544BAF0A78DD8');
        $this->addSql('ALTER TABLE assignment DROP FOREIGN KEY FK_30C544BA884B8635');
        $this->addSql('ALTER TABLE session DROP FOREIGN KEY FK_D044D5D43D77B39E');
        $this->addSql('ALTER TABLE attendance DROP FOREIGN KEY FK_6DE30D91613FECDF');
        $this->addSql('ALTER TABLE assignment DROP FOREIGN KEY FK_30C544BA591CC992');
        $this->addSql('ALTER TABLE session DROP FOREIGN KEY FK_D044D5D4591CC992');
        $this->addSql('ALTER TABLE course DROP FOREIGN KEY FK_169E6FB9C32A47EE');
        $this->addSql('ALTER TABLE rate DROP FOREIGN KEY FK_DFEC3F39C32A47EE');
        $this->addSql('ALTER TABLE season DROP FOREIGN KEY FK_F0E45BA9C32A47EE');
        $this->addSql('ALTER TABLE payment DROP FOREIGN KEY FK_6D28840DBC999F9F');
        $this->addSql('ALTER TABLE session DROP FOREIGN KEY FK_D044D5D4D4619D1A');
        $this->addSql('ALTER TABLE course DROP FOREIGN KEY FK_169E6FB9D4619D1A');
        $this->addSql('ALTER TABLE customer DROP FOREIGN KEY FK_81398E09A76ED395');
        $this->addSql('ALTER TABLE attendance DROP FOREIGN KEY FK_6DE30D914C3A3BB');
        $this->addSql('ALTER TABLE customer DROP FOREIGN KEY FK_81398E09C35E566A');
        $this->addSql('ALTER TABLE session DROP FOREIGN KEY FK_D044D5D45FB14BA7');
        $this->addSql('ALTER TABLE course DROP FOREIGN KEY FK_169E6FB95FB14BA7');
        $this->addSql('ALTER TABLE assignment DROP FOREIGN KEY FK_30C544BADD842E46');
        $this->addSql('ALTER TABLE attendance DROP FOREIGN KEY FK_6DE30D91DD842E46');
        $this->addSql('ALTER TABLE assignment DROP FOREIGN KEY FK_30C544BA9395C3F3');
        $this->addSql('ALTER TABLE attendance DROP FOREIGN KEY FK_6DE30D919395C3F3');
        $this->addSql('ALTER TABLE period DROP FOREIGN KEY FK_C5B81ECE4EC001D1');
        $this->addSql('ALTER TABLE session DROP FOREIGN KEY FK_D044D5D4CC80CD12');
        $this->addSql('ALTER TABLE course DROP FOREIGN KEY FK_169E6FB9CC80CD12');
        $this->addSql('DROP TABLE assignment');
        $this->addSql('DROP TABLE period');
        $this->addSql('DROP TABLE zone');
        $this->addSql('DROP TABLE discipline');
        $this->addSql('DROP TABLE place');
        $this->addSql('DROP TABLE session');
        $this->addSql('DROP TABLE course');
        $this->addSql('DROP TABLE school');
        $this->addSql('DROP TABLE rate');
        $this->addSql('DROP TABLE listing');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE attendance');
        $this->addSql('DROP TABLE payment');
        $this->addSql('DROP TABLE family');
        $this->addSql('DROP TABLE level');
        $this->addSql('DROP TABLE position');
        $this->addSql('DROP TABLE customer');
        $this->addSql('DROP TABLE season');
        $this->addSql('DROP TABLE age');
    }
}

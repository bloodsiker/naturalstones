<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180220105822 extends AbstractMigration
{
    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return "Users, Groups and associations between these two (UserBundle)";
    }

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // table for users
        $users = $schema->createTable('user_users');
        $users->addColumn('id', 'integer', array('unsigned' => true, 'notnull' => true, 'autoincrement' => true));
        $users->addColumn('username', 'string', array('length' => 255, 'notnull' => true));
        $users->addColumn('username_canonical', 'string', array('length' => 255, 'notnull' => true));
        $users->addColumn('email', 'string', array('length' => 255, 'notnull' => true));
        $users->addColumn('email_canonical', 'string', array('length' => 255, 'notnull' => true));
        $users->addColumn('enabled', 'boolean', array('notnull' => true));
        $users->addColumn('salt', 'string', array('length' => 255, 'notnull' => true));
        $users->addColumn('password', 'string', array('length' => 255, 'notnull' => true));
        $users->addColumn('last_login', 'datetime', array('notnull' => false));
        $users->addColumn('locked', 'boolean', array('notnull' => true));
        $users->addColumn('expired', 'boolean', array('notnull' => true));
        $users->addColumn('expires_at', 'datetime', array('notnull' => false));
        $users->addColumn('confirmation_token', 'string', array('length' => 255, 'notnull' => false));
        $users->addColumn('password_requested_at', 'datetime', array('notnull' => false));
        $users->addColumn('roles', 'text', array('length' => 4294967295, 'notnull' => true, 'comment' => '(DC2Type:array)'));
        $users->addColumn('credentials_expired', 'boolean', array('notnull' => true));
        $users->addColumn('credentials_expire_at', 'datetime', array('notnull' => false));
        $users->addColumn('created_at', 'datetime', array('notnull' => true));
        $users->addColumn('updated_at', 'datetime', array('notnull' => true));
        $users->addColumn('date_of_birth', 'datetime', array('notnull' => false));
        $users->addColumn('firstname', 'string', array('length' => 64, 'notnull' => false));
        $users->addColumn('lastname', 'string', array('length' => 64, 'notnull' => false));
        $users->addColumn('website', 'string', array('length' => 64, 'notnull' => false));
        $users->addColumn('biography', 'string', array('length' => 255, 'notnull' => false));
        $users->addColumn('gender', 'string', array('length' => 1, 'notnull' => false));
        $users->addColumn('locale', 'string', array('length' => 8, 'notnull' => false));
        $users->addColumn('timezone', 'string', array('length' => 64, 'notnull' => false));
        $users->addColumn('phone', 'string', array('length' => 64, 'notnull' => false));
        $users->addColumn('facebook_uid', 'string', array('length' => 255, 'notnull' => false));
        $users->addColumn('facebook_name', 'string', array('length' => 255, 'notnull' => false));
        $users->addColumn('facebook_data', 'text', array('length' => 4294967295, 'notnull' => false, 'comment' => '(DC2Type:json)'));
        $users->addColumn('twitter_uid', 'string', array('length' => 255, 'notnull' => false));
        $users->addColumn('twitter_name', 'string', array('length' => 255, 'notnull' => false));
        $users->addColumn('twitter_data', 'text', array('length' => 4294967295, 'notnull' => false, 'comment' => '(DC2Type:json)'));
        $users->addColumn('gplus_uid', 'string', array('length' => 255, 'notnull' => false));
        $users->addColumn('gplus_name', 'string', array('length' => 255, 'notnull' => false));
        $users->addColumn('gplus_data', 'text', array('length' => 4294967295, 'notnull' => false, 'comment' => '(DC2Type:json)'));
        $users->addColumn('token', 'string', array('length' => 255, 'notnull' => false));
        $users->addColumn('two_step_code', 'string', array('length' => 255, 'notnull' => false));
        $users->setPrimaryKey(array('id'));
        $users->addUniqueIndex(array('username_canonical'));
        $users->addUniqueIndex(array('email_canonical'));

        // table for groups
        $groups = $schema->createTable('user_groups');
        $groups->addColumn('id', 'integer', array('unsigned' => true, 'notnull' => true, 'autoincrement' => true));
        $groups->addColumn('name', 'string', array('length' => 255, 'notnull' => true));
        $groups->addColumn('roles', 'text', array('length' => 4294967295, 'notnull' => true, 'comment' => '(DC2Type:array)'));
        $groups->setPrimaryKey(array('id'));
        $groups->addUniqueIndex(array('name'));

        // association table between users and groups
        $usersGroups = $schema->createTable('user_users_groups');
        $usersGroups->addColumn('user_id', 'integer', array('unsigned' => true, 'notnull' => true));
        $usersGroups->addColumn('group_id', 'integer', array('unsigned' => true, 'notnull' => true));
        $usersGroups->setPrimaryKey(array('user_id', 'group_id'));
        $usersGroups->addForeignKeyConstraint($users, array('user_id'), array('id'), array('onDelete' => 'cascade'));
        $usersGroups->addForeignKeyConstraint($groups, array('group_id'), array('id'), array('onDelete' => 'cascade'));
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $schema->dropTable('user_users_groups');
        $schema->dropTable('user_groups');
        $schema->dropTable('user_users');
    }
}

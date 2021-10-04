<?php declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Class Version20190506091325
 */
final class Version20190506091325 extends AbstractMigration
{
    /**
     * @return string
     */
    public function getDescription()
    {
        return "Quiz, QuizAnswer, QuizResult (QuizBundle)";
    }

    /**
     * @param Schema $schema
     *
     * @throws \Doctrine\DBAL\Schema\SchemaException
     */
    public function up(Schema $schema)
    {
        // quiz
        $quiz = $schema->createTable('quiz_quiz');
        $quiz->addColumn('id', 'integer', array('unsigned' => true, 'notnull' => true, 'autoincrement' => true));
        $quiz->addColumn('title', 'string', array('length' => 255, 'notnull' => false));
        $quiz->addColumn('is_active', 'boolean', array('notnull' => true));
        $quiz->addColumn('created_at', 'datetime', array('notnull' => true));
        $quiz->addColumn('voted_type', 'integer', array('columnDefinition' => 'TINYINT(2) UNSIGNED DEFAULT 0 NOT NULL'));
        $quiz->addColumn('voted_count', 'integer', array('unsigned' => true, 'notnull' => false));
        $quiz->setPrimaryKey(['id']);

        // quiz answer
        $quizAnswer = $schema->createTable('quiz_quiz_answer');
        $quizAnswer->addColumn('id', 'integer', array('unsigned' => true, 'notnull' => true, 'autoincrement' => true));
        $quizAnswer->addColumn('title', 'string', array('length' => 255, 'notnull' => false));
        $quizAnswer->addColumn('link', 'string', array('length' => 255, 'notnull' => false));
        $quizAnswer->addColumn('percent', 'decimal', array('precision' => 6, 'scale' => 2, 'notnull' => true, 'default' => 0.00));
        $quizAnswer->addColumn('counter', 'integer', array('unsigned' => true, 'notnull' => true, 'default' => 0));
        $quizAnswer->setPrimaryKey(['id']);

        $quizHasAnswer = $schema->createTable('quiz_quiz_has_answer');
        $quizHasAnswer->addColumn('id', 'integer', array('unsigned' => true, 'notnull' => true, 'autoincrement' => true));
        $quizHasAnswer->addColumn('quiz_id', 'integer', array('unsigned' => true, 'notnull' => true));
        $quizHasAnswer->addColumn('answer_id', 'integer', array('unsigned' => true, 'notnull' => true));
        $quizHasAnswer->addColumn('order_num', 'integer', array('unsigned' => true, 'notnull' => true, 'default' => 0));
        $quizHasAnswer->setPrimaryKey(['id']);
        $quizHasAnswer->addForeignKeyConstraint($quiz, ['quiz_id'], ['id']);
        $quizHasAnswer->addForeignKeyConstraint($quizAnswer, ['answer_id'], ['id']);

        // quiz result
        $quizResult = $schema->createTable('quiz_quiz_result');
        $quizResult->addColumn('id', 'integer', array('notnull' => true, 'autoincrement' => true));
        $quizResult->addColumn('quiz_id', 'integer', array('unsigned' => true, 'notnull' => false));
        $quizResult->addColumn('answer_id', 'integer', array('unsigned' => true, 'notnull' => false));
        $quizResult->addColumn('comment', 'string', array('length' => 400, 'notnull' => false));
        $quizResult->addColumn('ip', 'bigint', array('unsigned' => true, 'notnull' => false));
        $quizResult->addColumn('voted_at', 'datetime', array('notnull' => true));
        $quizResult->setPrimaryKey(['id']);
        $quizResult->addForeignKeyConstraint($quiz, ['quiz_id'], ['id'], ['onDelete' => 'cascade', 'onUpdate' => 'restrict']);
        $quizResult->addForeignKeyConstraint($quizAnswer, ['answer_id'], ['id'], ['onDelete' => 'cascade', 'onUpdate' => 'restrict']);
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $schema->dropTable('quiz_quiz_result');
        $schema->dropTable('quiz_quiz_has_answer');

        $schema->dropTable('quiz_quiz_answer');

        $schema->dropTable('quiz_quiz');
    }
}

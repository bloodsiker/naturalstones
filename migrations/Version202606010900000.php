<?php

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version202606010900000 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $wheelSpinIds = $this->connection->fetchFirstColumn(
            'SELECT id FROM wheel_spin ORDER BY id ASC'
        );

        foreach ($wheelSpinIds as $wheelSpinId) {
            $options = $this->connection->fetchAllAssociative(
                'SELECT id, valuation FROM wheel_spin_has_option WHERE wheel_spin_id = ? ORDER BY order_num ASC',
                [$wheelSpinId]
            );

            if (!$options) {
                continue;
            }

            $valuations = array_map(static fn(array $row): int => (int) $row['valuation'], $options);
            $maxValuation = max($valuations);

            $weights = [];
            foreach ($options as $option) {
                $weights[(int) $option['id']] = max(1, $maxValuation - (int) $option['valuation'] + 1);
            }

            $totalWeight = array_sum($weights);
            if ($totalWeight <= 0) {
                continue;
            }

            foreach ($weights as $optionId => $weight) {
                $percent = round($weight * 100 / $totalWeight, 2);

                $this->addSql(sprintf(
                    'UPDATE wheel_spin_has_option SET percent = %s WHERE id = %d',
                    number_format($percent, 2, '.', ''),
                    $optionId
                ));
            }
        }
    }

    public function down(Schema $schema): void
    {
    }
}

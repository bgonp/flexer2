<?php

declare(strict_types=1);

namespace App\DQL;

use App\Entity\Course;
use App\Entity\Customer;
use App\Entity\Named;
use App\Utils\DayOfWeek;
use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\AST\PathExpression;
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;

class SearchString extends FunctionNode
{
    public function parse(Parser $parser)
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }

    public function getSql(SqlWalker $sqlWalker)
    {
        $expression = new PathExpression(PathExpression::TYPE_STATE_FIELD, '', '');
        $expression->type = PathExpression::TYPE_STATE_FIELD;
        $concat = [];
        $entities = [];
        foreach ($sqlWalker->getQueryComponents() as $alias => $data) {
            if (in_array($data['metadata']->name, $entities)) {
                continue;
            }
            $entities[] = $data['metadata']->name;

            if (Customer::class === $data['metadata']->name) {
                $concat = array_merge($concat, $this->getCustomerExpressions($alias, $expression, $sqlWalker));
            } elseif (Course::class === $data['metadata']->name) {
                $concat[] = $this->getDateExpression($alias, $expression, $sqlWalker);
                $concat[] = $this->getWeekDayExpression($alias, $expression, $sqlWalker);
            } elseif (Named::class === get_parent_class($data['metadata']->name)) {
                $concat[] = $this->getNameExpression($alias, $expression, $sqlWalker);
            }
        }

        return 'CONCAT_WS(\' \', '.implode(', ', $concat).')';
    }

    private function getCustomerExpressions(string $alias, PathExpression $expression, SqlWalker $sqlWalker): array
    {
        $expression->identificationVariable = $alias;
        $fields = ['name', 'surname', 'email', 'phone'];
        $expressions = [];
        foreach ($fields as $field) {
            $expression->field = $field;
            $expressions[] = $expression->dispatch($sqlWalker);
        }

        return $expressions;
    }

    private function getNameExpression(string $alias, PathExpression $expression, SqlWalker $sqlWalker): string
    {
        $expression->identificationVariable = $alias;
        $expression->field = 'name';

        return $expression->dispatch($sqlWalker);
    }

    private function getDateExpression(string $alias, PathExpression $expression, SqlWalker $sqlWalker): string
    {
        $expression->identificationVariable = $alias;
        $expression->field = 'time';

        return "DATE_FORMAT({$expression->dispatch($sqlWalker)}, '%H:%i')";
    }

    private function getWeekDayExpression(string $alias, PathExpression $expression, SqlWalker $sqlWalker): string
    {
        $expression->identificationVariable = $alias;
        $expression->field = 'dayOfWeek';
        $sql = "CASE {$expression->dispatch($sqlWalker)} ";
        foreach (DayOfWeek::getAll() as $dayOfWeek) {
            $sql .= "WHEN {$dayOfWeek->getId()} THEN '$dayOfWeek' ";
        }
        $sql .= 'END';

        return $sql;
    }
}

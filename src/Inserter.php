<?php

declare(strict_types=1);

namespace achertovsky\RadixTrie;

use achertovsky\RadixTrie\Entity\Node;
use achertovsky\RadixTrie\InsertRules\BaseRule;
use achertovsky\RadixTrie\InsertRules\AddLeafFromLeafRule;
use achertovsky\RadixTrie\InsertRules\BreakNodeInsertRule;
use achertovsky\RadixTrie\InsertRules\DontInsertExistingLeafRule;
use achertovsky\RadixTrie\InsertRules\MatchingNodeAndMatchingLeafRule;
use achertovsky\RadixTrie\InsertRules\AddLeafFromNodeWithSameLabelRule;

class Inserter
{
    private NodeSearcher $nodeSearcher;
    private array $rules;

    public function __construct(
    ) {
        $this->nodeSearcher = new NodeSearcher();
        //@todo: add easiest checks at beginning
        $this->rules = [
            new AddLeafFromLeafRule(),
            new DontInsertExistingLeafRule(),
            new MatchingNodeAndMatchingLeafRule(),
            new AddLeafFromNodeWithSameLabelRule(),
            new BreakNodeInsertRule(),
        ];
    }

    public function insert(
        Node $rootNode,
        string $word
    ): void {
        $closestNode = $this->nodeSearcher->search(
            $rootNode,
            $word
        );

        /**
         * @var BaseRule $rule
         */
        foreach ($this->rules as $rule) {
            if (!$rule->supports(
                $closestNode,
                $word
            )) {
                continue;
            }

            $rule->apply(
                $closestNode,
                $word
            );

            return;
        }
    }
}

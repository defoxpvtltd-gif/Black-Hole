<?php
declare(strict_types=1);

namespace App\Content;

final class SiteCatalog
{
    private const CATEGORY_META = [
        'platform' => [
            'label' => 'Platform',
            'eyebrow' => 'Core Platform',
            'description' => 'Foundational products for operating AI across teams, workflows, and knowledge.',
        ],
        'solutions' => [
            'label' => 'Solutions',
            'eyebrow' => 'Business Solutions',
            'description' => 'Role-based experiences that turn the AI layer into practical revenue, support, and execution systems.',
        ],
        'industries' => [
            'label' => 'Industries',
            'eyebrow' => 'Industry Blueprints',
            'description' => 'Pre-shaped journeys for regulated, operational, and high-velocity sectors.',
        ],
        'security' => [
            'label' => 'Security',
            'eyebrow' => 'Trust Layer',
            'description' => 'Controls, governance, and audit capabilities for professional deployments.',
        ],
        'resources' => [
            'label' => 'Resources',
            'eyebrow' => 'Learning Hub',
            'description' => 'Guides, academy content, templates, and proof-driven education for adoption.',
        ],
        'company' => [
            'label' => 'Company',
            'eyebrow' => 'Company',
            'description' => 'Brand, leadership, market position, partnerships, and business credibility.',
        ],
        'integrations' => [
            'label' => 'Integrations',
            'eyebrow' => 'Connected Stack',
            'description' => 'Connect the assistant to collaboration, storage, data, and orchestration systems.',
        ],
        'documentation' => [
            'label' => 'Documentation',
            'eyebrow' => 'Docs',
            'description' => 'Implementation references for operators, builders, and technical teams.',
        ],
    ];

    private const PAGE_SEEDS = [
        'platform' => [
            ['slug' => 'ai-workspace', 'title' => 'AI Workspace'],
            ['slug' => 'command-center', 'title' => 'Command Center'],
            ['slug' => 'knowledge-vault', 'title' => 'Knowledge Vault'],
        ],
        'solutions' => [
            ['slug' => 'customer-support-ai', 'title' => 'Customer Support AI'],
            ['slug' => 'sales-assistant', 'title' => 'Sales Assistant'],
            ['slug' => 'ops-copilot', 'title' => 'Ops Copilot'],
        ],
        'industries' => [
            ['slug' => 'saas-teams', 'title' => 'SaaS Teams'],
            ['slug' => 'ecommerce-growth', 'title' => 'E-Commerce Growth'],
            ['slug' => 'healthcare-operations', 'title' => 'Healthcare Operations'],
        ],
        'security' => [
            ['slug' => 'identity-access', 'title' => 'Identity and Access'],
            ['slug' => 'compliance-center', 'title' => 'Compliance Center'],
        ],
        'resources' => [
            ['slug' => 'case-studies', 'title' => 'Case Studies'],
            ['slug' => 'playbooks', 'title' => 'Playbooks'],
            ['slug' => 'academy', 'title' => 'Academy'],
        ],
        'company' => [
            ['slug' => 'about-brand', 'title' => 'About the Brand'],
            ['slug' => 'partner-program', 'title' => 'Partner Program'],
            ['slug' => 'leadership', 'title' => 'Leadership'],
        ],
        'integrations' => [
            ['slug' => 'slack-integration', 'title' => 'Slack Integration'],
            ['slug' => 'crm-sync', 'title' => 'CRM Sync'],
        ],
        'documentation' => [
            ['slug' => 'quickstart', 'title' => 'Quickstart'],
            ['slug' => 'deployment-guide', 'title' => 'Deployment Guide'],
            ['slug' => 'api-reference', 'title' => 'API Reference'],
        ],
    ];

    private static ?array $pages = null;

    public static function all(): array
    {
        self::boot();
        return array_values(self::$pages);
    }

    public static function count(): int
    {
        self::boot();
        return count(self::$pages);
    }

    public static function categories(): array
    {
        return self::CATEGORY_META;
    }

    public static function grouped(): array
    {
        self::boot();
        $grouped = [];
        foreach (self::CATEGORY_META as $key => $meta) {
            $grouped[$key] = [
                'meta' => $meta,
                'pages' => array_values(array_filter(self::$pages, static fn (array $page): bool => $page['category'] === $key)),
            ];
        }

        return $grouped;
    }

    public static function featured(int $limit = 8): array
    {
        self::boot();
        $featured = array_values(array_filter(self::$pages, static fn (array $page): bool => $page['featured']));
        return array_slice($featured, 0, $limit);
    }

    public static function latest(int $limit = 8): array
    {
        self::boot();
        return array_slice(array_values(array_reverse(self::$pages)), 0, $limit);
    }

    public static function byCategory(string $category, ?int $limit = null): array
    {
        self::boot();
        $pages = array_values(array_filter(self::$pages, static fn (array $page): bool => $page['category'] === $category));
        return $limit === null ? $pages : array_slice($pages, 0, $limit);
    }

    public static function find(string $slug): ?array
    {
        self::boot();
        return self::$pages[$slug] ?? null;
    }

    public static function related(string $slug, int $limit = 3): array
    {
        $current = self::find($slug);
        if ($current === null) {
            return [];
        }

        $related = array_values(array_filter(
            self::byCategory($current['category']),
            static fn (array $page): bool => $page['slug'] !== $slug
        ));

        return array_slice($related, 0, $limit);
    }

    private static function boot(): void
    {
        if (is_array(self::$pages)) {
            return;
        }

        self::$pages = [];
        foreach (self::PAGE_SEEDS as $category => $items) {
            $meta = self::CATEGORY_META[$category];
            foreach ($items as $index => $item) {
                $page = self::buildPage($category, $meta, $item, $index);
                self::$pages[$page['slug']] = $page;
            }
        }
    }

    private static function buildPage(string $category, array $meta, array $item, int $index): array
    {
        $title = $item['title'];
        $slug = $item['slug'];
        $featured = $index === 0;
        $summary = self::summaryFor($title, $meta['description']);
        $hero = self::heroFor($title, $meta['label']);
        $capabilities = self::capabilitiesFor($title, $meta['label']);
        $outcomes = self::outcomesFor($title, $meta['label']);
        $delivery = self::deliveryFor($title, $meta['label']);

        return [
            'slug' => $slug,
            'title' => $title,
            'category' => $category,
            'category_label' => $meta['label'],
            'eyebrow' => $meta['eyebrow'],
            'summary' => $summary,
            'hero' => $hero,
            'featured' => $featured,
            'metrics' => [
                ['label' => 'Launch speed', 'value' => ($index + 2) . ' weeks'],
                ['label' => 'Core modules', 'value' => (string) (4 + ($index % 3))],
                ['label' => 'Automation depth', 'value' => (string) (72 + ($index * 4)) . '%'],
            ],
            'highlights' => [
                $capabilities[0],
                $capabilities[1],
                $outcomes[0],
            ],
            'sections' => [
                [
                    'title' => 'What this page covers',
                    'content' => $summary,
                    'bullets' => $capabilities,
                ],
                [
                    'title' => 'How teams use it',
                    'content' => 'Professional teams use ' . $title . ' to shorten decision cycles, keep delivery visible, and create a more premium operating layer.',
                    'bullets' => $outcomes,
                ],
                [
                    'title' => 'Delivery model',
                    'content' => 'Each page is designed to stay readable for stakeholders, clean for admins, and flexible enough for future modules or integrations.',
                    'bullets' => $delivery,
                ],
            ],
        ];
    }

    private static function summaryFor(string $title, string $categoryDescription): string
    {
        return $title . ' gives the Black Hole platform a sharper, more curated destination for execution, insight, and scalable workflow design. ' . $categoryDescription;
    }

    private static function heroFor(string $title, string $categoryLabel): string
    {
        return 'Explore how ' . $title . ' fits into the ' . $categoryLabel . ' layer with a more focused, premium presentation for serious teams.';
    }

    private static function capabilitiesFor(string $title, string $categoryLabel): array
    {
        return [
            'A clear ' . strtolower($categoryLabel) . ' story built around ' . $title . ' instead of broad filler content.',
            'Reusable sections for hero messaging, trust signals, and conversion-ready product positioning.',
            'A cleaner foundation that can later connect to APIs, forms, dashboards, or CMS data without redesigning the page system.',
        ];
    }

    private static function outcomesFor(string $title, string $categoryLabel): array
    {
        return [
            'Better decision support for teams evaluating ' . $title . ' as a real operational capability.',
            'Sharper stakeholder communication through focused ' . strtolower($categoryLabel) . ' messaging and proof points.',
            'A more premium web presence that makes ' . $title . ' feel polished instead of overloaded.',
        ];
    }

    private static function deliveryFor(string $title, string $categoryLabel): array
    {
        return [
            'Responsive patterns for desktop product pages and mobile browsing without a second codebase.',
            'Navigation hooks that surface ' . $title . ' through the home page, library, and related page recommendations.',
            'A strong base for adding analytics, gated content, or logged-in tools to this ' . strtolower($categoryLabel) . ' experience.',
        ];
    }
}
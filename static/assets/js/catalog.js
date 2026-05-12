export const appName = "Black Hole AI Pro";
export const displayModel = "Black Hole V1.3";
export const socialHandle = "@ayat_rahman7690";
export const socialUrl = "https://www.instagram.com/ayat_rahman7690/";
export const defaultAssistantMessage = "Black Hole created by Ayat Rahman.";
export const suggestedPrompts = [
  "Create a clean project launch checklist for my website.",
  "Write a polished client proposal for my AI service.",
  "Explain this architecture in simple language.",
  "Who made you"
];

export const categoryMeta = {
  platform: {
    label: "Platform",
    eyebrow: "Core Platform",
    description: "Foundational products for operating AI across teams, workflows, and knowledge."
  },
  solutions: {
    label: "Solutions",
    eyebrow: "Business Solutions",
    description: "Role-based experiences that turn the AI layer into practical revenue, support, and execution systems."
  },
  industries: {
    label: "Industries",
    eyebrow: "Industry Blueprints",
    description: "Pre-shaped journeys for regulated, operational, and high-velocity sectors."
  },
  security: {
    label: "Security",
    eyebrow: "Trust Layer",
    description: "Controls, governance, and audit capabilities for professional deployments."
  },
  resources: {
    label: "Resources",
    eyebrow: "Learning Hub",
    description: "Guides, academy content, templates, and proof-driven education for adoption."
  },
  company: {
    label: "Company",
    eyebrow: "Company",
    description: "Brand, leadership, market position, partnerships, and business credibility."
  },
  integrations: {
    label: "Integrations",
    eyebrow: "Connected Stack",
    description: "Connect the assistant to collaboration, storage, data, and orchestration systems."
  },
  documentation: {
    label: "Documentation",
    eyebrow: "Docs",
    description: "Implementation references for operators, builders, and technical teams."
  }
};

const pageSeeds = {
  platform: [
    { slug: "ai-workspace", title: "AI Workspace" },
    { slug: "command-center", title: "Command Center" },
    { slug: "knowledge-vault", title: "Knowledge Vault" }
  ],
  solutions: [
    { slug: "customer-support-ai", title: "Customer Support AI" },
    { slug: "sales-assistant", title: "Sales Assistant" },
    { slug: "ops-copilot", title: "Ops Copilot" }
  ],
  industries: [
    { slug: "saas-teams", title: "SaaS Teams" },
    { slug: "ecommerce-growth", title: "E-Commerce Growth" },
    { slug: "healthcare-operations", title: "Healthcare Operations" }
  ],
  security: [
    { slug: "identity-access", title: "Identity and Access" },
    { slug: "compliance-center", title: "Compliance Center" }
  ],
  resources: [
    { slug: "case-studies", title: "Case Studies" },
    { slug: "playbooks", title: "Playbooks" },
    { slug: "academy", title: "Academy" }
  ],
  company: [
    { slug: "about-brand", title: "About the Brand" },
    { slug: "partner-program", title: "Partner Program" },
    { slug: "leadership", title: "Leadership" }
  ],
  integrations: [
    { slug: "slack-integration", title: "Slack Integration" },
    { slug: "crm-sync", title: "CRM Sync" }
  ],
  documentation: [
    { slug: "quickstart", title: "Quickstart" },
    { slug: "deployment-guide", title: "Deployment Guide" },
    { slug: "api-reference", title: "API Reference" }
  ]
};

function summaryFor(title, categoryDescription) {
  return `${title} gives the Black Hole platform a sharper, more curated destination for execution, insight, and scalable workflow design. ${categoryDescription}`;
}

function heroFor(title, categoryLabel) {
  return `Explore how ${title} fits into the ${categoryLabel} layer with a more focused, premium presentation for serious teams.`;
}

function capabilitiesFor(title, categoryLabel) {
  return [
    `A clear ${categoryLabel.toLowerCase()} story built around ${title} instead of broad filler content.`,
    "Reusable sections for hero messaging, trust signals, and conversion-ready product positioning.",
    "A cleaner foundation that can later connect to APIs, forms, dashboards, or CMS data without redesigning the page system."
  ];
}

function outcomesFor(title, categoryLabel) {
  return [
    `Better decision support for teams evaluating ${title} as a real operational capability.`,
    `Sharper stakeholder communication through focused ${categoryLabel.toLowerCase()} messaging and proof points.`,
    `A more premium web presence that makes ${title} feel polished instead of overloaded.`
  ];
}

function deliveryFor(title, categoryLabel) {
  return [
    "Responsive patterns for desktop product pages and mobile browsing without a second codebase.",
    `Navigation hooks that surface ${title} through the home page, library, and related page recommendations.`,
    `A strong base for adding analytics, gated content, or logged-in tools to this ${categoryLabel.toLowerCase()} experience.`
  ];
}

function buildPage(category, meta, item, index) {
  const title = item.title;
  const summary = summaryFor(title, meta.description);
  const capabilities = capabilitiesFor(title, meta.label);
  const outcomes = outcomesFor(title, meta.label);
  const delivery = deliveryFor(title, meta.label);

  return {
    slug: item.slug,
    title,
    category,
    categoryLabel: meta.label,
    eyebrow: meta.eyebrow,
    summary,
    hero: heroFor(title, meta.label),
    featured: index === 0,
    metrics: [
      { label: "Launch speed", value: `${index + 2} weeks` },
      { label: "Core modules", value: String(4 + (index % 3)) },
      { label: "Automation depth", value: `${72 + (index * 4)}%` }
    ],
    highlights: [capabilities[0], capabilities[1], outcomes[0]],
    sections: [
      {
        title: "What this page covers",
        content: summary,
        bullets: capabilities
      },
      {
        title: "How teams use it",
        content: `Professional teams use ${title} to shorten decision cycles, keep delivery visible, and create a more premium operating layer.`,
        bullets: outcomes
      },
      {
        title: "Delivery model",
        content: "Each page is designed to stay readable for stakeholders, clean for admins, and flexible enough for future modules or integrations.",
        bullets: delivery
      }
    ]
  };
}

const pages = Object.entries(pageSeeds).flatMap(([category, items]) => {
  const meta = categoryMeta[category];
  return items.map((item, index) => buildPage(category, meta, item, index));
});

export function allPages() {
  return pages.slice();
}

export function pageCount() {
  return pages.length;
}

export function categoryCount() {
  return Object.keys(categoryMeta).length;
}

export function groupedPages() {
  return Object.entries(categoryMeta).map(([key, meta]) => ({
    key,
    meta,
    pages: pages.filter((page) => page.category === key)
  }));
}

export function featuredPages(limit = 8) {
  return pages.filter((page) => page.featured).slice(0, limit);
}

export function latestPages(limit = 8) {
  return pages.slice().reverse().slice(0, limit);
}

export function byCategory(category, limit = null) {
  const results = pages.filter((page) => page.category === category);
  return typeof limit === "number" ? results.slice(0, limit) : results;
}

export function findPage(slug) {
  return pages.find((page) => page.slug === slug) || null;
}

export function relatedPages(slug, limit = 3) {
  const page = findPage(slug);
  if (!page) {
    return [];
  }

  return byCategory(page.category).filter((item) => item.slug !== slug).slice(0, limit);
}

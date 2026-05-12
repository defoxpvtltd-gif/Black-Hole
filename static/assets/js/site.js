import {
  appName,
  displayModel,
  socialHandle,
  socialUrl,
  pageCount,
  categoryCount,
  groupedPages,
  featuredPages,
  latestPages,
  byCategory,
  findPage,
  relatedPages
} from "./catalog.js";

const page = document.body.dataset.page || "home";
const year = new Date().getFullYear();

function escapeHtml(value) {
  return String(value)
    .replaceAll("&", "&amp;")
    .replaceAll("<", "&lt;")
    .replaceAll(">", "&gt;")
    .replaceAll('"', "&quot;")
    .replaceAll("'", "&#39;");
}

function urlForPage(slug) {
  return `page.html?slug=${encodeURIComponent(slug)}`;
}

function isActive(name) {
  return page === name ? " active" : "";
}

function renderHeader() {
  const target = document.getElementById("siteHeaderSlot");
  if (!target) {
    return;
  }

  target.innerHTML = `
    <header class="site-header">
      <div class="header-brand-block">
        <a class="brand" href="index.html">
          <span class="brand-dot" aria-hidden="true"></span>
          <span>
            <strong>${escapeHtml(appName)}</strong>
            <small>Curated AI product suite</small>
          </span>
        </a>
        <a class="brand-social-link" href="${escapeHtml(socialUrl)}" target="_blank" rel="noopener noreferrer">${escapeHtml(socialHandle)}</a>
        <div class="header-meta">
          <span class="meta-pill">${pageCount()} curated pages</span>
          <span class="meta-pill">${categoryCount()} core sections</span>
          <span class="meta-pill">${escapeHtml(displayModel)}</span>
        </div>
      </div>

      <div class="header-nav-block">
        <nav class="site-nav">
          <a class="nav-link${isActive("home")}" href="index.html">Home</a>
          <a class="nav-link${isActive("pages")}" href="pages.html">Library</a>
          <a class="nav-link${isActive("solutions")}" href="solutions.html">Solutions</a>
          <a class="nav-link${isActive("resources")}" href="resources.html">Resources</a>
          <a class="nav-link${isActive("pricing")}" href="pricing.html">Pricing</a>
          <a class="nav-link${isActive("about")}" href="about.html">About</a>
          <a class="nav-link${isActive("contact")}" href="contact.html">Contact</a>
        </nav>

        <div class="nav-actions">
          <a class="button primary${isActive("chat")}" href="chat.html">Open Chat</a>
          <a class="button subtle-button" href="contact.html">Request Setup</a>
        </div>
      </div>
    </header>`;
}

function renderFooter() {
  const target = document.getElementById("siteFooterSlot");
  if (!target) {
    return;
  }

  target.innerHTML = `
    <footer class="site-footer">
      <div class="footer-top">
        <section class="footer-brand">
          <span class="eyebrow">Platform footer</span>
          <h3>${escapeHtml(appName)}</h3>
          <p>A professional static AI product shell with a cleaner page library, premium web presentation, and a serverless chat workspace for modern deployment.</p>
          <div class="footer-badges">
            <span class="footer-chip">${pageCount()} curated pages</span>
            <span class="footer-chip">${categoryCount()} focused sections</span>
            <span class="footer-chip">Static site and serverless AI chat</span>
          </div>
          <div class="footer-credit-box">
            <strong>Ayat Rahman</strong>
            <span>Designed and developed by Ayat Rahman for the full Black Hole web experience.</span>
            <a class="footer-credit-link" href="${escapeHtml(socialUrl)}" target="_blank" rel="noopener noreferrer">${escapeHtml(socialHandle)}</a>
          </div>
        </section>

        <div class="footer-grid">
          <section class="footer-section">
            <h3>Platform</h3>
            <div class="footer-links">
              <a href="index.html">Home</a>
              <a href="pages.html">Curated library</a>
              <a href="solutions.html">Solutions</a>
              <a href="resources.html">Resources</a>
            </div>
          </section>

          <section class="footer-section">
            <h3>Company</h3>
            <div class="footer-links">
              <a href="about.html">About</a>
              <a href="pricing.html">Pricing</a>
              <a href="contact.html">Contact</a>
              <a href="industries.html">Industries</a>
            </div>
          </section>

          <section class="footer-section footer-cta">
            <h3>Next step</h3>
            <p>Use the curated site to build trust first, then move into the AI workspace or project inquiry flow.</p>
            <div class="action-row">
              <a class="button primary" href="chat.html">Open Chat</a>
              <a class="button subtle-button" href="contact.html">Request Setup</a>
            </div>
          </section>
        </div>
      </div>

      <div class="footer-bottom">
        <p>${year} ${escapeHtml(appName)} / Designed and developed by Ayat Rahman / AI, web, and interface engineering</p>
        <div class="footer-bottom-links">
          <a href="chat.html">Chat</a>
          <a href="pages.html">Library</a>
          <a href="contact.html">Contact</a>
        </div>
      </div>
    </footer>`;
}

function renderFeaturedGrid() {
  const target = document.getElementById("featuredGrid");
  if (!target) {
    return;
  }

  target.innerHTML = featuredPages(8).map((item) => `
    <article class="card link-card">
      <span class="eyebrow">${escapeHtml(item.categoryLabel)}</span>
      <h3>${escapeHtml(item.title)}</h3>
      <p>${escapeHtml(item.summary)}</p>
      <a class="text-link" href="${urlForPage(item.slug)}">Open page</a>
    </article>`).join("");
}

function renderCategoryGrid() {
  const target = document.getElementById("categoryGrid");
  if (!target) {
    return;
  }

  target.innerHTML = groupedPages().map((group) => `
    <article class="card category-card">
      <span class="eyebrow">${escapeHtml(group.meta.eyebrow)}</span>
      <h3>${escapeHtml(group.meta.label)}</h3>
      <p>${escapeHtml(group.meta.description)}</p>
      <div class="tag-row">
        <span class="tag">${group.pages.length} pages</span>
        <span class="tag">Focused layout</span>
      </div>
    </article>`).join("");
}

function renderLatestList() {
  const target = document.getElementById("latestList");
  if (!target) {
    return;
  }

  target.innerHTML = latestPages(6).map((item) => `
    <a class="list-link" href="${urlForPage(item.slug)}">
      <strong>${escapeHtml(item.title)}</strong>
      <span>${escapeHtml(item.categoryLabel)}</span>
    </a>`).join("");
}

function renderLibrary() {
  const target = document.getElementById("catalogGroups");
  if (!target) {
    return;
  }

  target.innerHTML = groupedPages().map((group) => `
    <article class="card category-block">
      <div class="section-heading compact">
        <span class="eyebrow">${escapeHtml(group.meta.eyebrow)}</span>
        <h2>${escapeHtml(group.meta.label)}</h2>
        <p>${escapeHtml(group.meta.description)}</p>
      </div>
      <div class="card-grid three-col">
        ${group.pages.map((item) => `
          <article class="card link-card slim-card">
            <h3>${escapeHtml(item.title)}</h3>
            <p>${escapeHtml(item.summary)}</p>
            <a class="text-link" href="${urlForPage(item.slug)}">View detail</a>
          </article>`).join("")}
      </div>
    </article>`).join("");
}

function renderCollection(category, elementId, eyebrowLabel) {
  const target = document.getElementById(elementId);
  if (!target) {
    return;
  }

  target.innerHTML = byCategory(category).map((item) => `
    <article class="card link-card slim-card">
      <span class="eyebrow">${escapeHtml(eyebrowLabel)}</span>
      <h3>${escapeHtml(item.title)}</h3>
      <p>${escapeHtml(item.summary)}</p>
      <a class="text-link" href="${urlForPage(item.slug)}">Open detail</a>
    </article>`).join("");
}

function renderResourcesCollection() {
  const target = document.getElementById("resourcesGrid");
  if (!target) {
    return;
  }

  const items = [...byCategory("resources"), ...byCategory("documentation")];
  target.innerHTML = items.map((item) => `
    <article class="card link-card slim-card">
      <span class="eyebrow">${escapeHtml(item.categoryLabel)}</span>
      <h3>${escapeHtml(item.title)}</h3>
      <p>${escapeHtml(item.summary)}</p>
      <a class="text-link" href="${urlForPage(item.slug)}">Open detail</a>
    </article>`).join("");
}

function renderPageDetail() {
  const stage = document.getElementById("pageDetailStage");
  if (!stage) {
    return;
  }

  const slug = new URLSearchParams(window.location.search).get("slug") || "";
  const item = findPage(slug);
  const titleEl = document.getElementById("dynamicPageTitle");
  const subtitleEl = document.getElementById("dynamicPageSubtitle");

  if (!item) {
    document.title = `${appName} - Page Not Found`;
    if (titleEl) titleEl.textContent = "Page not found";
    if (subtitleEl) subtitleEl.textContent = "The requested page could not be found in the website library.";
    stage.innerHTML = `
      <section class="content-grid form-centered">
        <article class="card feature-card">
          <span class="eyebrow">Missing page</span>
          <h2>The page you requested does not exist.</h2>
          <p>Return to the library and pick another destination from the professional content catalog.</p>
          <div class="action-row">
            <a class="button primary" href="pages.html">Open library</a>
            <a class="button subtle-button" href="index.html">Go home</a>
          </div>
        </article>
      </section>`;
    return;
  }

  document.title = `${appName} - ${item.title}`;
  if (titleEl) titleEl.textContent = item.title;
  if (subtitleEl) subtitleEl.textContent = item.hero;

  stage.innerHTML = `
    <section class="content-grid detail-layout">
      <article class="card detail-panel">
        <span class="eyebrow">${escapeHtml(item.eyebrow)}</span>
        <h2>${escapeHtml(item.title)}</h2>
        <p>${escapeHtml(item.summary)}</p>
        <div class="metric-strip">
          ${item.metrics.map((metric) => `
            <div class="mini-stat">
              <strong>${escapeHtml(metric.value)}</strong>
              <span>${escapeHtml(metric.label)}</span>
            </div>`).join("")}
        </div>
      </article>

      <aside class="card stat-card">
        <h3>Key highlights</h3>
        <ul class="feature-list">
          ${item.highlights.map((highlight) => `<li>${escapeHtml(highlight)}</li>`).join("")}
        </ul>
      </aside>
    </section>

    <section class="content-grid three-col">
      ${item.sections.map((section) => `
        <article class="card mini-card">
          <span class="eyebrow">${escapeHtml(item.categoryLabel)}</span>
          <h3>${escapeHtml(section.title)}</h3>
          <p>${escapeHtml(section.content)}</p>
          <ul class="feature-list tight-list">
            ${section.bullets.map((bullet) => `<li>${escapeHtml(bullet)}</li>`).join("")}
          </ul>
        </article>`).join("")}
    </section>

    <section class="content-grid section-stack">
      <div class="section-heading compact">
        <span class="eyebrow">Related pages</span>
        <h2>Continue exploring the same category</h2>
      </div>
      <div class="card-grid three-col">
        ${relatedPages(item.slug, 3).map((related) => `
          <article class="card link-card slim-card">
            <h3>${escapeHtml(related.title)}</h3>
            <p>${escapeHtml(related.summary)}</p>
            <a class="text-link" href="${urlForPage(related.slug)}">Read more</a>
          </article>`).join("")}
      </div>
    </section>`;
}

function bindCounters() {
  document.querySelectorAll("[data-page-count]").forEach((node) => {
    node.textContent = pageCount();
  });

  document.querySelectorAll("[data-category-count]").forEach((node) => {
    node.textContent = categoryCount();
  });
}

renderHeader();
renderFooter();
bindCounters();
renderFeaturedGrid();
renderCategoryGrid();
renderLatestList();
renderLibrary();
renderCollection("solutions", "solutionsGrid", "Solution");
renderCollection("industries", "industriesGrid", "Industry");
renderResourcesCollection();
renderPageDetail();

# Black Hole AI Pro

Netlify-ready version of Black Hole AI Pro built with:
- HTML
- CSS
- JavaScript
- Netlify Functions
- OpenRouter chat integration

## Project structure

- `static/` : deployable site files
- `static/assets/css/app.css` : main styling
- `static/assets/js/catalog.js` : page catalog and content engine
- `static/assets/js/site.js` : shared header, footer, and content rendering
- `static/assets/js/chat.js` : chat workspace UI and local session history
- `netlify/functions/chat.mjs` : serverless AI chat endpoint
- `netlify.toml` : Netlify publish and redirect config

## Netlify deploy

1. Push this project to GitHub.
2. In Netlify, import the repository.
3. Publish directory: `static`
4. Functions directory: `netlify/functions`
5. Add these environment variables in Netlify:

```env
OPENROUTER_API_KEY=your_openrouter_api_key_here
OPENROUTER_MODEL=openrouter/auto
OPENROUTER_FALLBACK_MODELS=openrouter/free
OPENROUTER_APP_NAME="Black Hole AI Pro"
OPENROUTER_APP_URL=https://your-netlify-site.netlify.app
```

## Main static pages

- `static/index.html`
- `static/pages.html`
- `static/page.html`
- `static/solutions.html`
- `static/resources.html`
- `static/industries.html`
- `static/about.html`
- `static/pricing.html`
- `static/contact.html`
- `static/chat.html`

## Chat behavior

- OpenRouter-powered AI chat through Netlify Functions
- Creator rule included: `Ayat Rahman is my creator.`
- Default greeting in chat: `Black Hole created by Ayat Rahman.`
- Local browser history for the chat session
- Mobile-friendly black and gold workspace UI

## Contact form

`static/contact.html` uses a Netlify form, so you can collect project inquiries without PHP or SQLite.

## Local preview

You can preview the static site with any simple static server. Example:

```powershell
cd "E:\XAMP\htdocs\Black Hole\php-blackhole-pro"
npx serve static
```

Or with Netlify local dev:

```powershell
cd "E:\XAMP\htdocs\Black Hole\php-blackhole-pro"
netlify dev
```

## Notes

- The old PHP version is still present in the repo, but the Netlify deployment uses `static/` and `netlify/functions/`.
- If an API key was previously exposed in example files, rotate it before deployment.

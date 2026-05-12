const CREATOR_RESPONSE = "Ayat Rahman is my creator.";
const GREETING_RESPONSE = "Hello! How can I help you today?";
const EMPTY_FALLBACK = "I could not generate a clean model response just now. Please try again or rephrase your message.";
const SYSTEM_PROMPT = "You are Black Hole, a professional AI assistant. Be clear, concise, helpful, and practical. If the user asks who created you, reply exactly: \"Ayat Rahman is my creator.\"";
const CREATOR_PATTERNS = [
  "who made you",
  "who make you",
  "who created you",
  "who create you",
  "who built you",
  "who is your creator",
  "kisne banaya",
  "kis ne banaya",
  "tumhe kisne banaya",
  "tumhe kis ne banaya",
  "aapko kisne banaya"
];
const GREETING_PATTERNS = ["hi", "hello", "hey", "salam", "assalamualaikum", "aoa"];

function json(body, status = 200) {
  return new Response(JSON.stringify(body), {
    status,
    headers: {
      "content-type": "application/json; charset=utf-8",
      "cache-control": "no-store"
    }
  });
}

function normalize(text) {
  return String(text || "")
    .toLowerCase()
    .replace(/[^a-z0-9\s]/g, " ")
    .replace(/\s+/g, " ")
    .trim();
}

function isCreatorQuestion(text) {
  const value = normalize(text);
  return CREATOR_PATTERNS.some((pattern) => value.includes(pattern));
}

function isGreeting(text) {
  return GREETING_PATTERNS.includes(normalize(text));
}

function trimHistory(history, maxHistory = 24) {
  if (!Array.isArray(history)) {
    return [];
  }

  const valid = history.filter((item) => item && (item.role === "user" || item.role === "assistant") && String(item.content || "").trim() !== "");
  return valid.slice(-maxHistory);
}

async function callOpenRouter({ message, history }) {
  const apiKey = (process.env.OPENROUTER_API_KEY || "").trim();
  const model = (process.env.OPENROUTER_MODEL || "openrouter/auto").trim();
  const fallbackModels = (process.env.OPENROUTER_FALLBACK_MODELS || "openrouter/free")
    .split(",")
    .map((item) => item.trim())
    .filter(Boolean);
  const appName = (process.env.OPENROUTER_APP_NAME || process.env.APP_NAME || "Black Hole AI Pro").trim();
  const appUrl = (process.env.OPENROUTER_APP_URL || process.env.URL || "https://example.com").trim();

  if (!apiKey) {
    return { ok: false, status: 500, error: "OPENROUTER_API_KEY is missing on the Netlify site." };
  }

  const messages = [{ role: "system", content: SYSTEM_PROMPT }];
  for (const item of trimHistory(history)) {
    messages.push({ role: item.role, content: String(item.content).trim() });
  }
  messages.push({ role: "user", content: message });

  const payload = {
    model,
    models: Array.from(new Set([model, ...fallbackModels])),
    route: "fallback",
    temperature: 0.7,
    messages
  };

  const response = await fetch("https://openrouter.ai/api/v1/chat/completions", {
    method: "POST",
    headers: {
      Authorization: `Bearer ${apiKey}`,
      "Content-Type": "application/json",
      Accept: "application/json",
      "HTTP-Referer": appUrl,
      "X-Title": appName
    },
    body: JSON.stringify(payload)
  });

  const data = await response.json().catch(() => ({}));
  if (!response.ok) {
    const errorMessage = data?.error?.message || `HTTP ${response.status}`;
    return { ok: false, status: response.status, error: errorMessage };
  }

  let content = data?.choices?.[0]?.message?.content ?? "";
  if (Array.isArray(content)) {
    content = content.map((segment) => segment?.text || "").join("");
  }

  const text = String(content || "").trim();
  if (!text) {
    return { ok: true, text: EMPTY_FALLBACK, model: "fallback-message" };
  }

  return {
    ok: true,
    text,
    model: String(data?.model || model),
    usage: data?.usage || null
  };
}

export default async (request) => {
  if (request.method === "OPTIONS") {
    return new Response(null, { status: 204, headers: { allow: "POST, OPTIONS" } });
  }

  if (request.method !== "POST") {
    return json({ ok: false, error: "Method not allowed." }, 405);
  }

  const body = await request.json().catch(() => ({}));
  const message = String(body?.message || "").trim();
  const history = Array.isArray(body?.history) ? body.history : [];

  if (!message) {
    return json({ ok: false, error: "Message is required." }, 400);
  }

  if (isCreatorQuestion(message)) {
    return json({
      ok: true,
      reply: CREATOR_RESPONSE,
      meta: { model: "rule-based", timestamp: new Date().toISOString() }
    });
  }

  if (isGreeting(message)) {
    return json({
      ok: true,
      reply: GREETING_RESPONSE,
      meta: { model: "local-greeting", timestamp: new Date().toISOString() }
    });
  }

  try {
    const result = await callOpenRouter({ message, history });
    if (!result.ok) {
      return json({ ok: false, error: result.error || "Chat request failed." }, result.status || 500);
    }

    return json({
      ok: true,
      reply: result.text,
      meta: {
        model: result.model,
        usage: result.usage,
        timestamp: new Date().toISOString()
      }
    });
  } catch (error) {
    return json({ ok: false, error: "Network error while contacting OpenRouter." }, 500);
  }
};

import { defaultAssistantMessage, displayModel, suggestedPrompts } from "./catalog.js";

const stageEl = document.getElementById("chatStage");
const messagesEl = document.getElementById("messages");
const emptyStateEl = document.getElementById("emptyState");
const formEl = document.getElementById("chatForm");
const inputEl = document.getElementById("messageInput");
const clearBtn = document.getElementById("clearChat");
const newBtn = document.getElementById("newChat");
const recentEl = document.getElementById("recentQuestions");
const modelLabelEl = document.getElementById("activeModelLabel");
const sidebarToggle = document.getElementById("sidebarToggle");
const sidebarClose = document.getElementById("sidebarClose");
const sidebarBackdrop = document.getElementById("sidebarBackdrop");
const quickPromptTargets = document.querySelectorAll("[data-prompt-list]");
const storageKey = "blackhole.static.chat.history";

if (!stageEl || !messagesEl || !emptyStateEl || !formEl || !inputEl) {
  throw new Error("Chat UI is missing required elements.");
}

const state = {
  history: loadHistory(),
  sending: false
};

function loadHistory() {
  try {
    const raw = window.localStorage.getItem(storageKey);
    const parsed = raw ? JSON.parse(raw) : [];
    return Array.isArray(parsed) ? parsed : [];
  } catch (error) {
    return [];
  }
}

function saveHistory() {
  window.localStorage.setItem(storageKey, JSON.stringify(state.history));
}

function ensureDefaultAssistantMessage() {
  if (state.history.length > 0) {
    return;
  }

  state.history.push({
    role: "assistant",
    content: defaultAssistantMessage,
    timestamp: new Date().toISOString()
  });
  saveHistory();
}

function nowLabel(value = "") {
  const date = value ? new Date(value) : new Date();
  if (Number.isNaN(date.getTime())) {
    return new Date().toLocaleTimeString([], { hour: "2-digit", minute: "2-digit" });
  }

  return date.toLocaleTimeString([], { hour: "2-digit", minute: "2-digit" });
}

function prefersReducedMotion() {
  return window.matchMedia && window.matchMedia("(prefers-reduced-motion: reduce)").matches;
}

function closeSidebar() {
  document.body.classList.remove("sidebar-open");
}

function openSidebar() {
  document.body.classList.add("sidebar-open");
}

function autoResize() {
  inputEl.style.height = "auto";
  inputEl.style.height = `${Math.min(inputEl.scrollHeight, 180)}px`;
}

function scrollBottom() {
  window.requestAnimationFrame(() => {
    stageEl.scrollTo({ top: stageEl.scrollHeight, behavior: "smooth" });
  });
}

function updateEmptyState() {
  const hasMessages = state.history.length > 0;
  emptyStateEl.hidden = hasMessages;
  messagesEl.hidden = !hasMessages;
}

function createThinkingNode() {
  const wrap = document.createElement("div");
  wrap.className = "chat-thinking";

  const orb = document.createElement("span");
  orb.className = "chat-thinking-orb";

  const copy = document.createElement("div");
  copy.className = "chat-thinking-copy";

  const title = document.createElement("strong");
  title.textContent = "Mind glowing";

  const dots = document.createElement("span");
  dots.className = "chat-thinking-dots";
  dots.setAttribute("aria-hidden", "true");
  dots.innerHTML = "<i></i><i></i><i></i>";

  copy.append(title, dots);
  wrap.append(orb, copy);
  return wrap;
}

function setThinkingState(bubble) {
  bubble.classList.add("thinking-mode");
  bubble.textContent = "";
  bubble.appendChild(createThinkingNode());
}

function messageName(role) {
  return role === "assistant" ? "Black Hole" : "";
}

function createMessage(role, text, options = {}) {
  const row = document.createElement("article");
  row.className = `chat-row ${role}`;

  const body = document.createElement("div");
  body.className = "chat-message-body";

  const meta = document.createElement("div");
  meta.className = "chat-message-meta";

  const name = messageName(role);
  if (name) {
    const author = document.createElement("div");
    author.className = "chat-author";
    author.textContent = name;
    meta.appendChild(author);
  }

  const stamp = document.createElement("div");
  stamp.className = "chat-time";
  stamp.textContent = nowLabel(options.timestamp || "");
  meta.appendChild(stamp);

  const bubble = document.createElement("div");
  bubble.className = `chat-bubble${options.pending ? " pending" : ""}`;
  if (options.pending) {
    setThinkingState(bubble);
  } else {
    bubble.textContent = text;
  }

  body.append(meta, bubble);

  if (role === "assistant") {
    const avatar = document.createElement("div");
    avatar.className = "chat-avatar";
    row.append(avatar);
  }

  row.appendChild(body);
  messagesEl.appendChild(row);
  scrollBottom();

  return { bubble, stamp };
}

function renderPromptButtons() {
  quickPromptTargets.forEach((target) => {
    target.innerHTML = suggestedPrompts.map((prompt) => {
      const css = target.dataset.promptList === "grid" ? "chat-prompt-card" : "sidebar-prompt";
      return `<button class="${css}" type="button" data-prompt="${prompt.replaceAll('"', '&quot;')}">${css === "chat-prompt-card" ? `<strong>${prompt}</strong><span>Tap to send this prompt</span>` : prompt}</button>`;
    }).join("");
  });
}

function renderRecentQuestions() {
  if (!recentEl) {
    return;
  }

  const seen = new Set();
  const prompts = [];
  for (let index = state.history.length - 1; index >= 0; index -= 1) {
    const item = state.history[index];
    if (!item || item.role !== "user") {
      continue;
    }

    const text = String(item.content || "").trim();
    const key = text.toLowerCase();
    if (!text || seen.has(key)) {
      continue;
    }

    seen.add(key);
    prompts.push(text);
    if (prompts.length >= 6) {
      break;
    }
  }

  if (prompts.length === 0) {
    recentEl.innerHTML = '<p class="sidebar-empty">Start a conversation and your recent prompts will appear here.</p>';
    return;
  }

  recentEl.innerHTML = prompts.map((prompt) => `<button type="button" class="sidebar-prompt recent-prompt" data-prompt="${prompt.replaceAll('"', '&quot;')}">${prompt}</button>`).join("");
}

function renderHistory() {
  messagesEl.innerHTML = "";
  state.history.forEach((item) => {
    createMessage(item.role === "assistant" ? "assistant" : "user", String(item.content || ""), {
      timestamp: item.timestamp || ""
    });
  });
  updateEmptyState();
  renderRecentQuestions();
  scrollBottom();
}

function addHistory(role, content, timestamp = new Date().toISOString()) {
  state.history.push({ role, content, timestamp });
  saveHistory();
  updateEmptyState();
  renderRecentQuestions();
}

function setModelLabel() {
  if (modelLabelEl) {
    modelLabelEl.textContent = displayModel;
  }
}

function animateAssistantReply(bubble, text) {
  return new Promise((resolve) => {
    const content = String(text || "");
    bubble.classList.remove("thinking-mode");
    bubble.textContent = "";

    if (!content || prefersReducedMotion() || content.length > 900) {
      bubble.textContent = content;
      resolve();
      return;
    }

    let index = 0;
    const step = Math.max(1, Math.ceil(content.length / 80));

    function tick() {
      index = Math.min(content.length, index + step);
      bubble.textContent = content.slice(0, index);
      scrollBottom();
      if (index < content.length) {
        window.setTimeout(tick, 16);
      } else {
        resolve();
      }
    }

    tick();
  });
}

async function updatePendingMessage(view, text) {
  view.bubble.classList.remove("pending");
  await animateAssistantReply(view.bubble, text);
  view.stamp.textContent = nowLabel();
}

async function postChat(message) {
  const historyForModel = state.history.slice(-18).map((item) => ({
    role: item.role,
    content: item.content
  }));

  const response = await fetch("/api/chat", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ message, history: historyForModel })
  });

  const data = await response.json().catch(() => ({}));
  return { ok: response.ok, status: response.status, data };
}

async function sendMessage(rawText) {
  const prompt = String(rawText || "").trim();
  if (!prompt || state.sending) {
    return;
  }

  state.sending = true;
  closeSidebar();

  const sentAt = new Date().toISOString();
  createMessage("user", prompt, { timestamp: sentAt });
  addHistory("user", prompt, sentAt);
  inputEl.value = "";
  autoResize();

  const pendingView = createMessage("assistant", "Thinking...", { pending: true });

  try {
    const result = await postChat(prompt);
    if (!result.ok || !result.data.ok) {
      const errorText = result.data.error || "I could not reach the model right now. Please try again in a moment.";
      await updatePendingMessage(pendingView, errorText);
      addHistory("assistant", errorText);
    } else {
      const reply = result.data.reply || "No response.";
      await updatePendingMessage(pendingView, reply);
      addHistory("assistant", reply, result.data.meta?.timestamp || new Date().toISOString());
      setModelLabel();
    }
  } catch (error) {
    const errorText = "Network error. Please try again.";
    await updatePendingMessage(pendingView, errorText);
    addHistory("assistant", errorText);
  } finally {
    state.sending = false;
    inputEl.focus();
    scrollBottom();
  }
}

async function clearConversation() {
  state.history = [];
  saveHistory();
  ensureDefaultAssistantMessage();
  renderHistory();
  inputEl.focus();
  autoResize();
  setModelLabel();
  closeSidebar();
}

formEl.addEventListener("submit", (event) => {
  event.preventDefault();
  sendMessage(inputEl.value);
});

inputEl.addEventListener("input", autoResize);
inputEl.addEventListener("keydown", (event) => {
  if (event.key === "Enter" && !event.shiftKey) {
    event.preventDefault();
    sendMessage(inputEl.value);
  }
});

clearBtn?.addEventListener("click", clearConversation);
newBtn?.addEventListener("click", clearConversation);
sidebarToggle?.addEventListener("click", openSidebar);
sidebarClose?.addEventListener("click", closeSidebar);
sidebarBackdrop?.addEventListener("click", closeSidebar);

document.addEventListener("click", (event) => {
  const promptButton = event.target.closest("[data-prompt]");
  if (!promptButton) {
    return;
  }
  const prompt = promptButton.getAttribute("data-prompt") || "";
  sendMessage(prompt);
});

document.addEventListener("keydown", (event) => {
  if (event.key === "Escape") {
    closeSidebar();
  }
});

renderPromptButtons();
setModelLabel();
ensureDefaultAssistantMessage();
renderHistory();
autoResize();
inputEl.focus();

if (window.innerWidth <= 980) {
  closeSidebar();
}

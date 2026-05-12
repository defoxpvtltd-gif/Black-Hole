(function () {
  "use strict";

  const config = window.APP_CONFIG || {};
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

  if (!stageEl || !messagesEl || !emptyStateEl || !formEl || !inputEl) {
    return;
  }

  const state = {
    history: Array.isArray(config.initialHistory) ? config.initialHistory.slice() : [],
    sending: false,
  };

  function defaultAssistantMessage() {
    const value = config.defaultAssistantMessage;
    return value && String(value).trim() !== ""
      ? String(value).trim()
      : "Black Hole created by Ayat Rahman.";
  }

  function ensureDefaultAssistantMessage() {
    if (state.history.length > 0) {
      return;
    }

    state.history.push({
      role: "assistant",
      content: defaultAssistantMessage(),
      timestamp: new Date().toISOString(),
    });
  }

  function prefersReducedMotion() {
    return window.matchMedia && window.matchMedia("(prefers-reduced-motion: reduce)").matches;
  }

  function nowLabel() {
    return new Date().toLocaleTimeString([], { hour: "2-digit", minute: "2-digit" });
  }

  function formatLabel(value) {
    if (!value) {
      return nowLabel();
    }

    const date = new Date(value);
    if (Number.isNaN(date.getTime())) {
      return nowLabel();
    }

    return date.toLocaleTimeString([], { hour: "2-digit", minute: "2-digit" });
  }

  function closeSidebar() {
    document.body.classList.remove("sidebar-open");
  }

  function openSidebar() {
    document.body.classList.add("sidebar-open");
  }

  function setModelLabel() {
    if (!modelLabelEl) {
      return;
    }

    modelLabelEl.textContent = config.modelLabel && String(config.modelLabel).trim() !== ""
      ? String(config.modelLabel).trim()
      : "Black Hole V1.3";
  }

  function autoResize() {
    inputEl.style.height = "auto";
    inputEl.style.height = Math.min(inputEl.scrollHeight, 180) + "px";
  }

  function scrollBottom() {
    window.requestAnimationFrame(function () {
      stageEl.scrollTo({ top: stageEl.scrollHeight, behavior: "smooth" });
    });
  }

  function updateEmptyState() {
    emptyStateEl.hidden = state.history.length > 0;
    messagesEl.hidden = state.history.length === 0;
  }

  function messageName(role) {
    return role === "assistant" ? "Black Hole" : "";
  }

  function avatarLabel(role) {
    return role === "assistant" ? "BH" : "";
  }

  function createMessage(role, text, options) {
    const settings = options || {};
    const row = document.createElement("article");
    row.className = "chat-row " + role;

    const body = document.createElement("div");
    body.className = "chat-message-body";

    const meta = document.createElement("div");
    meta.className = "chat-message-meta";

    const authorText = messageName(role);
    if (authorText) {
      const author = document.createElement("div");
      author.className = "chat-author";
      author.textContent = authorText;
      meta.appendChild(author);
    }

    const stamp = document.createElement("div");
    stamp.className = "chat-time";
    stamp.textContent = formatLabel(settings.timestamp || "");
    meta.appendChild(stamp);

    const bubble = document.createElement("div");
    bubble.className = "chat-bubble" + (settings.pending ? " pending" : "");
    if (settings.pending) {
      setThinkingState(bubble);
    } else {
      bubble.textContent = text;
    }

    body.appendChild(meta);
    body.appendChild(bubble);

    if (role === "assistant") {
      const avatar = document.createElement("div");
      avatar.className = "chat-avatar";
      avatar.textContent = avatarLabel(role);
      row.appendChild(avatar);
    }

    row.appendChild(body);
    messagesEl.appendChild(row);
    scrollBottom();

    return {
      row: row,
      bubble: bubble,
      stamp: stamp,
    };
  }

  function renderRecentQuestions() {
    if (!recentEl) {
      return;
    }

    recentEl.innerHTML = "";

    const seen = new Set();
    const prompts = [];

    for (let index = state.history.length - 1; index >= 0; index -= 1) {
      const item = state.history[index];
      if (!item || item.role !== "user") {
        continue;
      }

      const text = String(item.content || "").trim();
      const normalized = text.toLowerCase();
      if (!text || seen.has(normalized)) {
        continue;
      }

      seen.add(normalized);
      prompts.push(text);

      if (prompts.length >= 6) {
        break;
      }
    }

    if (prompts.length === 0) {
      const empty = document.createElement("p");
      empty.className = "sidebar-empty";
      empty.textContent = "Start a conversation and your recent prompts will appear here.";
      recentEl.appendChild(empty);
      return;
    }

    prompts.forEach(function (prompt) {
      const button = document.createElement("button");
      button.type = "button";
      button.className = "sidebar-prompt recent-prompt";
      button.setAttribute("data-prompt", prompt);
      button.textContent = prompt;
      recentEl.appendChild(button);
    });
  }

  function renderHistory() {
    messagesEl.innerHTML = "";
    state.history.forEach(function (item) {
      createMessage(item.role === "assistant" ? "assistant" : "user", String(item.content || ""), {
        timestamp: item.timestamp || "",
      });
    });
    updateEmptyState();
    renderRecentQuestions();
    scrollBottom();
  }

  function addHistory(role, text, timestamp) {
    state.history.push({
      role: role,
      content: text,
      timestamp: timestamp || new Date().toISOString(),
    });
    renderRecentQuestions();
    updateEmptyState();
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

    copy.appendChild(title);
    copy.appendChild(dots);
    wrap.appendChild(orb);
    wrap.appendChild(copy);
    return wrap;
  }

  function setThinkingState(bubble) {
    bubble.classList.add("thinking-mode");
    bubble.textContent = "";
    bubble.appendChild(createThinkingNode());
  }

  function animateAssistantReply(bubble, text) {
    return new Promise(function (resolve) {
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

  async function postJson(url, body) {
    const response = await fetch(url, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "X-CSRF-Token": config.csrfToken || "",
      },
      body: JSON.stringify(body || {}),
    });

    let data = {};
    try {
      data = await response.json();
    } catch (error) {
      data = {};
    }

    return { ok: response.ok, status: response.status, data: data };
  }

  async function sendMessage(text) {
    const prompt = String(text || "").trim();
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

    const loadingView = createMessage("assistant", "Thinking...", { pending: true });

    try {
      const res = await postJson(config.chatEndpoint, { message: prompt });

      if (res.status === 401 && config.loginUrl) {
        window.location.href = config.loginUrl;
        return;
      }

      if (!res.ok || !res.data.ok) {
        let errorText = res.data.error || "I could not reach the model right now. Please try again in a moment.";
        if (res.data.diagnostic) {
          errorText += "\nPHP: " + (res.data.diagnostic.php || "unknown")
            + " | cURL: " + (res.data.diagnostic.curl || "unknown")
            + " | SSL: " + (res.data.diagnostic.ssl || "unknown");
        }
        await updatePendingMessage(loadingView, errorText);
        addHistory("assistant", errorText);
      } else {
        const reply = res.data.reply || "No response.";
        await updatePendingMessage(loadingView, reply);
        addHistory("assistant", reply, res.data.meta && res.data.meta.timestamp ? res.data.meta.timestamp : new Date().toISOString());
        setModelLabel();
      }
    } catch (error) {
      const message = "Network error. Please try again.";
      await updatePendingMessage(loadingView, message);
      addHistory("assistant", message);
    } finally {
      state.sending = false;
      inputEl.focus();
      scrollBottom();
    }
  }

  async function clearConversation() {
    try {
      await postJson(config.clearEndpoint, {});
    } catch (error) {
      // Ignore clear endpoint failure and reset local view anyway.
    }

    state.history = [];
    ensureDefaultAssistantMessage();
    messagesEl.innerHTML = "";
    renderRecentQuestions();
    updateEmptyState();
    inputEl.focus();
    autoResize();
    setModelLabel();
    closeSidebar();
  }

  formEl.addEventListener("submit", function (event) {
    event.preventDefault();
    sendMessage(inputEl.value);
  });

  inputEl.addEventListener("input", autoResize);
  inputEl.addEventListener("keydown", function (event) {
    if (event.key === "Enter" && !event.shiftKey) {
      event.preventDefault();
      sendMessage(inputEl.value);
    }
  });

  if (clearBtn) {
    clearBtn.addEventListener("click", clearConversation);
  }

  if (newBtn) {
    newBtn.addEventListener("click", clearConversation);
  }

  if (sidebarToggle) {
    sidebarToggle.addEventListener("click", openSidebar);
  }

  if (sidebarClose) {
    sidebarClose.addEventListener("click", closeSidebar);
  }

  if (sidebarBackdrop) {
    sidebarBackdrop.addEventListener("click", closeSidebar);
  }

  document.addEventListener("click", function (event) {
    const promptButton = event.target.closest("[data-prompt]");
    if (!promptButton) {
      return;
    }

    const prompt = promptButton.getAttribute("data-prompt") || "";
    sendMessage(prompt);
  });

  document.addEventListener("keydown", function (event) {
    if (event.key === "Escape") {
      closeSidebar();
    }
  });

  setModelLabel();
  ensureDefaultAssistantMessage();
  renderHistory();
  autoResize();
  inputEl.focus();

  if (window.innerWidth <= 980) {
    closeSidebar();
  }
})();

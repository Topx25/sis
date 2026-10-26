/**
 * Injects admin-configured integration snippets (chat widget, analytics /
 * search-engine head code) into the public site. Snippets come from the
 * backend /site-config endpoint and are only present when enabled in the
 * dashboard Settings → Integrations tab.
 *
 * Note: <script> tags set via innerHTML do not execute, so each script node is
 * re-created as a real element. External scripts still require their domain to
 * be allowed by the page CSP.
 */

const API_BASE = (import.meta.env.VITE_API_BASE_URL || "").replace(/\/+$/, "");

function injectHtml(html, target) {
  if (!html || !target) return;

  const tpl = document.createElement("template");
  tpl.innerHTML = html;

  Array.from(tpl.content.childNodes).forEach((node) => {
    if (node.tagName === "SCRIPT") {
      const s = document.createElement("script");
      for (const attr of node.attributes) {
        s.setAttribute(attr.name, attr.value);
      }
      s.text = node.textContent || "";
      target.appendChild(s);
    } else {
      target.appendChild(node.cloneNode(true));
    }
  });
}

export async function injectIntegrations() {
  if (typeof window === "undefined" || window.__sisIntegrationsInjected) return;

  try {
    const res = await fetch(`${API_BASE}/site-config`, {
      headers: { Accept: "application/json" },
    });
    const json = await res.json();
    const intg = json?.data?.integrations;

    if (!intg || !intg.enabled) return;

    window.__sisIntegrationsInjected = true;
    injectHtml(intg.head_code, document.head);
    injectHtml(intg.body_code, document.body);
    injectHtml(intg.chat_code, document.body);
  } catch {
    // Fail silently — integrations are optional.
  }
}

<script setup>
import { ref, computed, onMounted } from "vue";
import ToastNotification from "@/components/global/ToastNotification.vue";
import AdminPageHeader from "@/SIS-Dashboard/components/AdminPageHeader.vue";
import { adminAPI } from "@/SIS-Dashboard/api/index.js";

const loading = ref(true);
const messages = ref([]);
const page = ref(1);
const pagination = ref({ total: 0, current_page: 1, total_pages: 1 });
const statusFilter = ref("all");
const selectedId = ref(null);
const replyText = ref("");
const sending = ref(false);
const outbox = ref([]);
const showToast = ref(false);
const toast = ref({ title: "", message: "", variant: "success" });

const filtered = computed(() =>
  statusFilter.value === "all"
    ? messages.value
    : messages.value.filter((m) => m.status === statusFilter.value)
);

const counts = computed(() => {
  const total = messages.value.length;
  const unread = messages.value.filter((m) => m.status === "unread").length;
  const read = messages.value.filter((m) => m.status === "read").length;
  const archived = messages.value.filter((m) => m.status === "archived").length;
  return { total, unread, read, archived };
});

const tabs = computed(() => [
  { key: "all", label: "All", count: counts.value.total },
  { key: "unread", label: "Unread", count: counts.value.unread },
  { key: "read", label: "Read", count: counts.value.read },
  { key: "archived", label: "Archived", count: counts.value.archived },
]);

const selected = computed(() => messages.value.find((m) => m.id === selectedId.value) || null);

const statusMeta = {
  unread: { label: "Unread", cls: "bg-sis-accent/10 text-sis-accent", dot: "bg-sis-accent" },
  read: { label: "Read", cls: "bg-sis-panel text-sis-muted", dot: "bg-sis-muted" },
  archived: { label: "Archived", cls: "bg-sis-border/40 text-sis-muted", dot: "bg-sis-border" },
};

const initials = (name) =>
  String(name || "")
    .split(" ")
    .map((w) => w[0])
    .filter(Boolean)
    .slice(0, 2)
    .join("")
    .toUpperCase();

const fmtDate = (iso) => (iso ? new Date(iso).toLocaleString() : "");

const timeAgo = (iso) => {
  if (!iso) return "";
  const d = new Date(iso);
  const mins = Math.floor((Date.now() - d.getTime()) / 60000);
  if (mins < 1) return "just now";
  if (mins < 60) return `${mins}m ago`;
  const hrs = Math.floor(mins / 60);
  if (hrs < 24) return `${hrs}h ago`;
  return `${Math.floor(hrs / 24)}d ago`;
};

function notify(title, message, variant) {
  toast.value = { title, message, variant };
  showToast.value = true;
}

async function load(targetPage) {
  const toPage = targetPage ?? page.value;
  loading.value = true;
  try {
    const res = await adminAPI.getContactRequests({ page: toPage });
    page.value = res.pagination?.current_page ?? toPage;
    messages.value = res.requests;
    pagination.value = res.pagination;
    if (!selectedId.value && messages.value.length) selectedId.value = messages.value[0].id;
  } catch (e) {
    notify("Load failed", e.message || "Could not load messages.", "error");
  } finally {
    loading.value = false;
  }
}

async function loadOutbox() {
  try {
    outbox.value = await adminAPI.getOutbox();
  } catch {
    /* ignore in mock / before auth */
  }
}

async function changeStatus(id, status) {
  try {
    const updated = await adminAPI.updateContactStatus(id, status);
    const idx = messages.value.findIndex((m) => m.id === id);
    if (idx !== -1) messages.value[idx] = { ...messages.value[idx], ...updated };
    notify("Status updated", `Message marked as "${status}".`, "success");
  } catch (e) {
    notify("Update failed", e.message, "error");
  }
}

async function sendReply() {
  if (!selected.value) return;
  const text = replyText.value.trim();
  if (text.length < 10) {
    notify("Reply too short", "Please write at least 10 characters.", "error");
    return;
  }
  sending.value = true;
  try {
    const updated = await adminAPI.replyContact(selected.value.id, text);
    const idx = messages.value.findIndex((m) => m.id === selected.value.id);
    if (idx !== -1) {
      // Backend does not persist the reply yet (see frontend_requerments.md §3),
      // so reflect it locally so the thread shows the sent reply immediately.
      messages.value[idx] = {
        ...messages.value[idx],
        ...updated,
        reply: text,
        replied_at: new Date().toISOString(),
        status: "read",
      };
    }
    notify("Reply sent", `Email sent to ${updated.email} (simulated in mock).`, "success");
    replyText.value = "";
    await loadOutbox();
  } catch (e) {
    notify("Reply failed", e.message, "error");
  } finally {
    sending.value = false;
  }
}

onMounted(() => {
  load();
  loadOutbox();
});
</script>

<template>
  <div class="space-y-6">
    <!-- Header -->
    <AdminPageHeader
      title="Contact Messages"
      subtitle="Inquiries submitted through the website contact form. Reply directly to send email responses."
      category="Communication"
      icon="mail"
    >
      <template #actions>
        <button
          type="button"
          @click="load"
          :disabled="loading"
          class="inline-flex items-center gap-1.5 rounded-xl border border-sis-border bg-white px-4 py-2.5 text-sm font-semibold text-sis-text shadow-sm transition hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-50"
        >
          <span class="material-symbols-outlined text-[18px]" :class="{ 'animate-spin': loading }">refresh</span>
          Refresh
        </button>
      </template>
    </AdminPageHeader>

    <!-- Tabs -->
    <div class="flex flex-wrap gap-2">
      <button
        v-for="tab in tabs"
        :key="tab.key"
        @click="statusFilter = tab.key"
        class="rounded-full px-4 py-1.5 text-sm font-medium transition-colors"
        :class="
          statusFilter === tab.key
            ? 'bg-sis-primary text-white'
            : 'bg-white text-sis-muted border border-sis-border hover:text-sis-text'
        "
      >
        {{ tab.label }}
        <span :class="statusFilter === tab.key ? 'text-white/70' : 'text-sis-muted/70'">({{ tab.count }})</span>
      </button>
    </div>

    <!-- Body -->
    <div class="grid gap-6 lg:grid-cols-5">
      <!-- List -->
      <div class="lg:col-span-2 space-y-3">
        <div v-if="loading" class="space-y-3">
          <div v-for="n in 4" :key="n" class="animate-pulse rounded-xl border border-sis-border bg-white p-4 shadow-sis">
            <div class="flex items-center gap-3">
              <div class="h-10 w-10 rounded-full bg-sis-panel"></div>
              <div class="flex-1 space-y-2">
                <div class="h-3 w-24 rounded bg-sis-panel"></div>
                <div class="h-2.5 w-16 rounded bg-sis-panel"></div>
              </div>
            </div>
            <div class="mt-3 h-2.5 w-full rounded bg-sis-panel"></div>
          </div>
        </div>

        <button
          v-for="m in filtered"
          :key="m.id"
          @click="selectedId = m.id"
          class="w-full text-left rounded-xl border p-4 transition-colors shadow-sis"
          :class="
            selectedId === m.id
              ? 'border-sis-accent bg-white ring-1 ring-sis-accent/30'
              : 'border-sis-border bg-white hover:border-sis-accent/50'
          "
        >
          <div class="flex items-center gap-3">
            <span
              class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-sis-primary/10 text-sm font-semibold text-sis-primary"
            >
              {{ initials(m.name) }}
            </span>
            <div class="min-w-0 flex-1">
              <div class="flex items-center justify-between gap-2">
                <p class="truncate text-sm font-semibold text-sis-text">{{ m.name }}</p>
                <span class="flex-shrink-0 text-[11px] text-sis-muted">{{ timeAgo(m.created_at) }}</span>
              </div>
              <p class="truncate text-xs text-sis-muted">{{ m.company_name || m.email }}</p>
            </div>
          </div>
          <p class="mt-2.5 line-clamp-2 text-[13px] leading-relaxed text-sis-text/80">{{ m.message }}</p>
          <div class="mt-3 flex items-center gap-2">
            <span
              class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-[11px] font-semibold"
              :class="statusMeta[m.status]?.cls"
            >
              <span class="h-1.5 w-1.5 rounded-full" :class="statusMeta[m.status]?.dot"></span>
              {{ statusMeta[m.status]?.label }}
            </span>
            <span v-if="m.replied_at" class="text-[11px] text-sis-accent">Replied</span>
          </div>
        </button>

        <div v-if="!loading && filtered.length === 0" class="rounded-xl border border-dashed border-sis-border bg-white p-8 text-center">
          <span class="material-symbols-outlined text-[32px] text-sis-muted/50">mark_email_unread</span>
          <p class="mt-2 text-sm text-sis-muted">No {{ statusFilter === "all" ? "" : statusFilter }} messages.</p>
        </div>

        <div v-if="pagination.total_pages > 1" class="flex items-center justify-between pt-2 text-sm text-sis-muted">
          <span>Page {{ pagination.current_page }} of {{ pagination.total_pages }}</span>
          <div class="flex gap-2">
            <button
              :disabled="pagination.current_page <= 1"
              @click="load(pagination.current_page - 1)"
              class="rounded-lg border border-sis-border bg-white px-3 py-1 disabled:opacity-40"
            >
              Prev
            </button>
            <button
              :disabled="pagination.current_page >= pagination.total_pages"
              @click="load(pagination.current_page + 1)"
              class="rounded-lg border border-sis-border bg-white px-3 py-1 disabled:opacity-40"
            >
              Next
            </button>
          </div>
        </div>
      </div>

      <!-- Detail -->
      <div class="lg:col-span-3">
        <div v-if="selected" class="rounded-xl border border-sis-border bg-white shadow-sis">
          <div class="border-b border-sis-border p-6">
            <div class="flex flex-wrap items-start justify-between gap-4">
              <div class="flex items-center gap-4">
                <span class="flex h-12 w-12 items-center justify-center rounded-full bg-sis-primary/10 text-base font-semibold text-sis-primary">
                  {{ initials(selected.name) }}
                </span>
                <div>
                  <p class="font-headline-md text-[15px] font-semibold text-sis-text">{{ selected.name }}</p>
                  <p class="text-sm text-sis-muted">{{ selected.email }}</p>
                </div>
              </div>
              <span
                class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-semibold"
                :class="statusMeta[selected.status]?.cls"
              >
                <span class="h-1.5 w-1.5 rounded-full" :class="statusMeta[selected.status]?.dot"></span>
                {{ statusMeta[selected.status]?.label }}
              </span>
            </div>
            <div class="mt-4 grid gap-2 text-sm text-sis-muted sm:grid-cols-2">
              <p><span class="font-medium text-sis-text">Phone:</span> {{ selected.phone || "—" }}</p>
              <p><span class="font-medium text-sis-text">Company:</span> {{ selected.company_name || "—" }}</p>
              <p><span class="font-medium text-sis-text">Received:</span> {{ fmtDate(selected.created_at) }}</p>
              <p v-if="selected.replied_at"><span class="font-medium text-sis-text">Replied:</span> {{ fmtDate(selected.replied_at) }}</p>
            </div>
          </div>

          <div class="space-y-5 p-6">
            <div>
              <p class="mb-2 text-xs font-semibold uppercase tracking-wider text-sis-muted">Message</p>
              <p class="whitespace-pre-wrap rounded-lg bg-sis-panel p-4 text-sm leading-relaxed text-sis-text">
                {{ selected.message }}
              </p>
            </div>

            <!-- Existing reply -->
            <div v-if="selected.reply" class="rounded-lg border border-sis-accent/30 bg-sis-accent/5 p-4">
              <p class="mb-2 flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-sis-accent">
                <span class="material-symbols-outlined text-[16px]">forward_to_inbox</span>
                Your reply (emailed to {{ selected.email }})
              </p>
              <p class="whitespace-pre-wrap text-sm leading-relaxed text-sis-text">{{ selected.reply }}</p>
            </div>

            <!-- Reply box -->
            <div>
              <label class="mb-2 block text-xs font-semibold uppercase tracking-wider text-sis-muted">
                Reply by email
              </label>
              <textarea
                v-model="replyText"
                rows="4"
                placeholder="Write your reply… it will be emailed to the user's inbox."
                class="w-full rounded-lg border border-sis-border bg-white p-4 text-sm text-sis-text placeholder:text-sis-muted/70 focus:border-sis-accent focus:outline-none focus:ring-1 focus:ring-sis-accent/30"
              ></textarea>
              <div class="mt-3 flex flex-wrap items-center gap-2">
                <button
                  @click="sendReply"
                  :disabled="sending"
                  class="flex items-center gap-2 rounded-lg bg-sis-accent px-5 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-sis-light disabled:opacity-50"
                >
                  <span v-if="sending" class="material-symbols-outlined animate-spin text-[18px]">progress_activity</span>
                  <span v-else class="material-symbols-outlined text-[18px]">send</span>
                  {{ sending ? "Sending…" : "Send Reply" }}
                </button>
                <span class="text-[11px] text-sis-muted">Mock sends to {{ selected.email }} and logs it below.</span>
              </div>
            </div>

            <!-- Status actions -->
            <div class="flex flex-wrap items-center gap-2 border-t border-sis-border pt-4">
              <span class="mr-1 text-xs font-semibold uppercase tracking-wider text-sis-muted">Mark as</span>
              <button
                v-if="selected.status !== 'read'"
                @click="changeStatus(selected.id, 'read')"
                class="rounded-lg border border-sis-border px-3 py-1.5 text-xs font-medium text-sis-text hover:bg-sis-panel"
              >
                Read
              </button>
              <button
                v-if="selected.status !== 'unread'"
                @click="changeStatus(selected.id, 'unread')"
                class="rounded-lg border border-sis-border px-3 py-1.5 text-xs font-medium text-sis-text hover:bg-sis-panel"
              >
                Unread
              </button>
              <button
                v-if="selected.status !== 'archived'"
                @click="changeStatus(selected.id, 'archived')"
                class="rounded-lg border border-sis-border px-3 py-1.5 text-xs font-medium text-sis-text hover:bg-sis-panel"
              >
                Archive
              </button>
            </div>
          </div>
        </div>

        <div v-else class="rounded-xl border border-dashed border-sis-border bg-white p-10 text-center">
          <span class="material-symbols-outlined text-[36px] text-sis-muted/50">chat_bubble_outline</span>
          <p class="mt-2 text-sm text-sis-muted">Select a message to view details and reply.</p>
        </div>
      </div>
    </div>

    <!-- Mock email log -->
    <section v-if="outbox.length" class="rounded-xl border border-sis-border bg-white shadow-sis">
      <div class="flex items-center gap-2 border-b border-sis-border px-6 py-4">
        <span class="material-symbols-outlined text-[18px] text-sis-accent">outbox</span>
        <h3 class="text-sm font-semibold text-sis-text">Simulated email log (mock)</h3>
        <span class="ml-auto rounded-full bg-sis-panel px-2.5 py-0.5 text-[11px] font-medium text-sis-muted">
          {{ outbox.length }} sent
        </span>
      </div>
      <ul class="divide-y divide-sis-border">
        <li v-for="(mail, i) in outbox" :key="i" class="flex flex-wrap items-center gap-3 px-6 py-3 text-sm">
          <span class="material-symbols-outlined text-[18px] text-sis-accent">forward_to_inbox</span>
          <span class="font-medium text-sis-text">{{ mail.to }}</span>
          <span class="text-sis-muted">· {{ mail.subject }}</span>
          <span class="ml-auto text-[11px] text-sis-muted">{{ fmtDate(mail.sent_at) }}</span>
        </li>
      </ul>
    </section>

    <ToastNotification
      v-if="showToast"
      :title="toast.title"
      :message="toast.message"
      :variant="toast.variant"
      @close="showToast = false"
    />
  </div>
</template>
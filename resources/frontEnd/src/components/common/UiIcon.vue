<template>
  <span
    v-if="!isSvgIcon"
    class="material-symbols-outlined select-none inline-flex items-center justify-center text-current leading-none shrink-0"
    :style="{
      fontSize: formattedSize,
      width: formattedSize,
      height: formattedSize,
      fontVariationSettings: `'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24`,
    }"
    aria-hidden="true"
  >
    {{ resolvedMaterialSymbol }}
  </span>

  <svg
    v-else
    viewBox="0 0 24 24"
    fill="none"
    stroke="currentColor"
    stroke-width="1.7"
    stroke-linecap="round"
    stroke-linejoin="round"
    aria-hidden="true"
    :width="size"
    :height="size"
  >
    <!-- =========================================================
         GENERAL
    ========================================================== -->

    <!-- monitor / dashboard -->
    <template v-if="iconName === 'monitor'">
      <rect x="2" y="3" width="20" height="14" rx="2" />
      <path d="M8 21h8M12 17v4M6 8h.01M9 8h6" />
    </template>

    <!-- predictive -->
    <template v-else-if="iconName === 'predictive'">
      <path d="M12 2v4M12 18v4M2 12h4M18 12h4" />
      <circle cx="12" cy="12" r="4" />
    </template>

    <!-- cube -->
    <template v-else-if="iconName === 'cube'">
      <path
        d="M21 16V8a2 2 0 0 0-1-1.7l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.7l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16zM3.3 7 12 12l8.7-5M12 22V12"
      />
    </template>

    <!-- layers -->
    <template v-else-if="iconName === 'layers'">
      <path d="m2 7 10-4 10 4-10 4zM2 7v10l10 4 10-4V7" />
      <path d="m5 12 7 3 7-3" />
    </template>

    <!-- energy -->
    <template v-else-if="iconName === 'energy'">
      <path d="M13 2 3 14h7l-1 8 10-12h-7z" />
    </template>

    <!-- database -->
    <template v-else-if="iconName === 'database'">
      <ellipse cx="12" cy="5" rx="9" ry="3" />
      <path d="M3 5v6c0 1.7 4 3 9 3s9-1.3 9-3V5" />
      <path d="M3 11v6c0 1.7 4 3 9 3s9-1.3 9-3v-6" />
    </template>

    <!-- connect / network -->
    <template v-else-if="iconName === 'connect' || iconName === 'network'">
      <circle cx="5" cy="12" r="2" />
      <circle cx="19" cy="6" r="2" />
      <circle cx="19" cy="18" r="2" />
      <path d="m7 11 10-4M7 13l10 4" />
    </template>

    <!-- pulse -->
    <template v-else-if="iconName === 'pulse'">
      <path d="M3 12h4l3 8 4-16 3 8h4" />
    </template>

    <!-- chart -->
    <template v-else-if="iconName === 'chart'">
      <path d="M4 20v-8M10 20V8M16 20v-4" />
      <path d="M3 20h18" />
    </template>

    <!-- robot -->
    <template v-else-if="iconName === 'robot-arm'">
      <path d="M5 20h14M12 20V8M8 8h8l4 4H4z" />
      <circle cx="12" cy="5" r="2" />
    </template>

    <!-- cpu -->
    <template v-else-if="iconName === 'cpu'">
      <rect x="4" y="4" width="16" height="16" rx="2" />
      <rect x="8" y="8" width="8" height="8" rx="1" />
      <path d="M2 12h2M20 12h2M12 2v2M12 20v2" />
    </template>

    <!-- settings / engineering -->
    <template v-else-if="iconName === 'settings'">
      <circle cx="12" cy="12" r="3" />
      <path
        d="M12 1v2M12 21v2M1 12h2M21 12h2M4.2 4.2l1.4 1.4M18.4 18.4l1.4 1.4M4.2 19.8l1.4-1.4M18.4 5.6l1.4-1.4"
      />
    </template>

    <!-- arrow -->
    <template v-else-if="iconName === 'arrow'">
      <path d="M5 12h14M13 6l6 6-6 6" />
    </template>

    <!-- chevron -->
    <template v-else-if="iconName === 'chevron-down' || iconName === 'chevron_down'">
      <path d="M6 9l6 6 6-6" />
    </template>
    <template v-else-if="iconName === 'chevron-left' || iconName === 'chevron_left'">
      <path d="m15 18-6-6 6-6" />
    </template>
    <template v-else-if="iconName === 'chevron-right' || iconName === 'chevron_right'">
      <path d="m9 18 6-6-6-6" />
    </template>
    <template v-else-if="iconName === 'chevron-up' || iconName === 'chevron_up'">
      <path d="m18 15-6-6-6 6" />
    </template>

    <!-- search -->
    <template v-else-if="iconName === 'search'">
      <circle cx="11" cy="11" r="6.5" />
      <path d="m16 16 4.5 4.5" />
    </template>

    <!-- menu -->
    <template v-else-if="iconName === 'menu'">
      <path d="M4 7h16M4 12h16M4 17h16" />
    </template>

    <!-- close -->
    <template v-else-if="iconName === 'close'">
      <path d="M6 6l12 12M18 6 6 18" />
    </template>

    <!-- =========================================================
         INDUSTRIES
    ========================================================== -->

    <!-- oil / oil-gas -->
    <template v-else-if="iconName === 'oil' || iconName === 'oil-gas'">
      <path
        d="M12 2s-6 7-6 12a6 6 0 0 0 12 0c0-5-6-12-6-12Z"
      />
      <path d="M9 15c.5 1.5 1.5 2.3 3 2.5" />
    </template>

    <!-- power -->
    <template v-else-if="iconName === 'power-energy' || iconName === 'power'">
      <path d="M13 2 3 14h7l-1 8 10-12h-7z" />
    </template>

    <!-- water -->
    <template v-else-if="iconName === 'water'">
      <path
        d="M12 2S5 10 5 15a7 7 0 0 0 14 0c0-5-7-13-7-13Z"
      />
      <path d="M9 16c.7 1.3 1.7 2 3 2" />
    </template>

    <!-- factory / manufacturing -->
    <template v-else-if="iconName === 'factory' || iconName === 'manufacturing'">
      <path d="M3 21V10l7 4v-4l7 4V6h4v15Z" />
      <path d="M7 18h2M13 18h2M18 18h2" />
    </template>

    <!-- pharmaceutical / science -->
    <template v-else-if="iconName === 'science' || iconName === 'pharmaceutical'">
      <path
        d="M9 3h6M10 3v6l-5 9a2 2 0 0 0 2 3h10a2 2 0 0 0 2-3l-5-9V3"
      />
      <path d="M7.5 16h9" />
    </template>

    <!-- chemical -->
    <template v-else-if="iconName === 'chemical'">
      <path d="M9 3h6M10 3v7l-5 8a2 2 0 0 0 2 3h10a2 2 0 0 0 2-3l-5-8V3" />
      <path d="M7 16h10" />
    </template>

    <!-- infrastructure -->
    <template v-else-if="iconName === 'infrastructure'">
      <path d="M4 21V5l8-3 8 3v16" />
      <path d="M4 21h16" />
      <path d="M8 9h2M14 9h2M8 13h2M14 13h2M8 17h2M14 17h2" />
    </template>

    <!-- building -->
    <template v-else-if="iconName === 'building'">
      <path d="M4 21V5l8-3 8 3v16" />
      <path d="M4 21h16" />
      <path d="M8 9h2M14 9h2M8 13h2M14 13h2M8 17h2M14 17h2" />
    </template>

    <!-- =========================================================
         BADGES
    ========================================================== -->

    <!-- quality -->
    <template v-else-if="iconName === 'quality'">
      <path d="M12 2l8 4v6c0 5-3.4 8.5-8 10-4.6-1.5-8-5-8-10V6l8-4Z" />
      <path d="m9 12 2 2 4-4" />
    </template>

    <!-- expert -->
    <template v-else-if="iconName === 'expert'">
      <circle cx="9" cy="8" r="3" />
      <circle cx="17" cy="9" r="2.5" />
      <path d="M3 20v-2a5 5 0 0 1 5-5h2a5 5 0 0 1 5 5v2" />
      <path d="M15 14h1a4 4 0 0 1 4 4v2" />
    </template>

    <!-- support -->
    <template v-else-if="iconName === 'support'">
      <circle cx="12" cy="12" r="9" />
      <path d="M12 7v5l3 2" />
    </template>

    <!-- AI -->
    <template v-else-if="iconName === 'ai' || iconName === 'artificial-intelligence'">
      <rect x="4" y="4" width="16" height="16" rx="3" />
      <path d="M8 9h8M8 13h5M8 17h8" />
      <path d="M2 9h2M2 15h2M20 9h2M20 15h2M9 2v2M15 2v2M9 20v2M15 20v2" />
    </template>

    <!-- insights -->
    <template v-else-if="iconName === 'insights'">
      <path d="M4 19V9M10 19V5M16 19v-7M22 19H2" />
      <path d="m4 7 5-3 6 4 5-5" />
    </template>

    <!-- security -->
    <template v-else-if="iconName === 'security'">
      <path d="M12 2l9 5v6c0 5.5-4.8 10.5-9 11-4.2-.5-9-5.5-9-11V7l9-5Z" />
      <path d="m9 12 2 2 4-4" />
    </template>

    <!-- scalable -->
    <template v-else-if="iconName === 'scalable'">
      <path d="M4 20 20 4" />
      <path d="M14 4h6v6" />
      <path d="M4 14v6h6" />
    </template>

    <!-- manufacturing intelligence -->
    <template v-else-if="iconName === 'manufacturing-intelligence'">
      <path d="M3 21V10l7 4v-4l7 4V6h4v15Z" />
      <circle cx="8" cy="8" r="2" />
      <path d="M8 5V3M5 8H3M11 8h2M8 11v2" />
    </template>

    <!-- operational efficiency -->
    <template v-else-if="iconName === 'operational-efficiency'">
      <path d="M4 19V5M4 19h16" />
      <path d="m7 15 4-4 3 2 6-7" />
      <path d="M16 6h4v4" />
    </template>

    <!-- connected enterprise -->
    <template v-else-if="iconName === 'connected-enterprise'">
      <circle cx="6" cy="12" r="2.5" />
      <circle cx="18" cy="6" r="2.5" />
      <circle cx="18" cy="18" r="2.5" />
      <path d="m8.5 11 7-4M8.5 13l7 4" />
    </template>

    <!-- precision -->
    <template v-else-if="iconName === 'precision'">
      <circle cx="12" cy="12" r="7" />
      <circle cx="12" cy="12" r="2" />
      <path d="M12 2v3M12 19v3M2 12h3M19 12h3" />
    </template>

    <!-- safety -->
    <template v-else-if="iconName === 'safety'">
      <path d="M12 2l8 4v6c0 5-3.4 8.5-8 10-4.6-1.5-8-5-8-10V6l8-4Z" />
      <path d="M12 7v5M12 16h.01" />
    </template>

    <!-- excellence -->
    <template v-else-if="iconName === 'excellence'">
      <path d="m12 3 2.8 5.7L21 9.6l-4.5 4.4 1 6.2-5.5-2.9-5.5 2.9 1-6.2L3 9.6l6.2-.9L12 3Z" />
    </template>

    <!-- =========================================================
         AUTOMATION / DIGITAL
    ========================================================== -->

    <!-- integration -->
    <template v-else-if="iconName === 'integration'">
      <circle cx="6" cy="12" r="3" />
      <circle cx="18" cy="6" r="3" />
      <circle cx="18" cy="18" r="3" />
      <path d="m8.5 11 7-4M8.5 13l7 4" />
    </template>

    <!-- testing -->
    <template v-else-if="iconName === 'testing'">
      <path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2" />
      <path d="M9 5a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2M9 5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2" />
      <path d="m8 14 2 2 5-5" />
    </template>

    <!-- commissioning -->
    <template v-else-if="iconName === 'commissioning'">
      <path d="M10.3 4.3c.4-1.8 3-1.8 3.4 0a1.7 1.7 0 0 0 2.6 1.1c1.5-.9 3.3.8 2.4 2.4a1.7 1.7 0 0 0 1.1 2.6c1.8.4 1.8 3 0 3.4a1.7 1.7 0 0 0-1.1 2.6c.9 1.5-.9 3.3-2.4 2.4a1.7 1.7 0 0 0-2.6 1.1c-.4 1.8-3 1.8-3.4 0a1.7 1.7 0 0 0-2.6-1.1c-1.5.9-3.3-.9-2.4-2.4a1.7 1.7 0 0 0-1.1-2.6c-1.8-.4-1.8-3 0-3.4A1.7 1.7 0 0 0 5.3 7.8c-.9-1.6.9-3.3 2.4-2.4a1.7 1.7 0 0 0 2.6-1.1Z" />
      <circle cx="12" cy="12" r="3" />
    </template>

    <!-- upgrade -->
    <template v-else-if="iconName === 'upgrade'">
      <path d="M4 20 20 4" />
      <path d="M14 4h6v6" />
      <path d="M4 14v6h6" />
    </template>

    <!-- design -->
    <template v-else-if="iconName === 'design'">
      <path d="M4 20 16 8" />
      <path d="m14 6 4 4" />
      <path d="m17 3 4 4-3 3-4-4 3-3Z" />
      <path d="M3 21h6" />
    </template>

    <!-- documentation -->
    <template v-else-if="iconName === 'documentation'">
      <path d="M6 3h9l4 4v14H6z" />
      <path d="M14 3v5h5M9 13h6M9 17h6" />
    </template>

    <!-- survey -->
    <template v-else-if="iconName === 'survey'">
      <path d="M12 21s7-6.2 7-12a7 7 0 1 0-14 0c0 5.8 7 12 7 12Z" />
      <circle cx="12" cy="9" r="2.5" />
    </template>

    <!-- feasibility -->
    <template v-else-if="iconName === 'feasibility'">
      <path d="M4 19V5M4 19h16" />
      <path d="M7 15v-4M12 15V7M17 15V4" />
    </template>

    <!-- project management -->
    <template v-else-if="iconName === 'project-management'">
      <circle cx="9" cy="7" r="3" />
      <path d="M3 20v-1a6 6 0 0 1 12 0v1" />
      <path d="M16 11a3 3 0 1 1 0 6M18 20h3v-1a5 5 0 0 0-5-5" />
    </template>

    <!-- lifecycle -->
    <template v-else-if="iconName === 'lifecycle'">
      <path d="M4 4v5h5" />
      <path d="M20 20v-5h-5" />
      <path d="M4.5 9A8 8 0 0 1 18 5.5L20 7" />
      <path d="M19.5 15A8 8 0 0 1 6 18.5L4 17" />
    </template>

    <!-- =========================================================
         DIGITAL SOLUTIONS
    ========================================================== -->

    <!-- MES -->
    <template v-else-if="iconName === 'mes'">
      <rect x="3" y="4" width="18" height="16" rx="2" />
      <path d="M7 8h10M7 12h4M14 12h3M7 16h10" />
    </template>

    <!-- CMMS -->
    <template v-else-if="iconName === 'cmms'">
      <circle cx="12" cy="12" r="3" />
      <path d="M19 12a7 7 0 0 0-.2-1.6l2-1.5-2-3.4-2.4 1a7 7 0 0 0-2.7-1.6L13.4 2h-4l-.4 2.9a7 7 0 0 0-2.7 1.6l-2.4-1-2 3.4 2 1.5A7 7 0 0 0 3.7 12c0 .6.1 1.1.2 1.6l-2 1.5 2 3.4 2.4-1a7 7 0 0 0 2.7 1.6l.4 2.9h4l.4-2.9a7 7 0 0 0 2.7-1.6l2.4 1 2-3.4-2-1.5c.1-.5.2-1 .2-1.6Z" />
    </template>

    <!-- enterprise management -->
    <template v-else-if="iconName === 'enterprise-management'">
      <path d="M4 21V5l8-3 8 3v16" />
      <path d="M4 21h16M8 9h2M14 9h2M8 13h2M14 13h2M8 17h2M14 17h2" />
    </template>

    <!-- IIoT -->
    <template v-else-if="iconName === 'iiot'">
      <circle cx="12" cy="12" r="3" />
      <path d="M5 12a7 7 0 0 1 14 0M2 12a10 10 0 0 1 20 0" />
      <path d="M5 12a7 7 0 0 0 14 0M2 12a10 10 0 0 0 20 0" />
    </template>

    <!-- cloud -->
    <template v-else-if="iconName === 'cloud-integration'">
      <path d="M7 18h10a4 4 0 0 0 .5-8 6 6 0 0 0-11.7 1A3.5 3.5 0 0 0 7 18Z" />
      <path d="M8 13h8M12 10v6" />
    </template>

    <!-- data analytics -->
    <template v-else-if="iconName === 'data-analytics'">
      <path d="M4 20V10M10 20V6M16 20v-8M22 20H2" />
      <path d="m4 7 5-3 6 4 5-5" />
    </template>

    <!-- dashboard -->
    <template v-else-if="iconName === 'dashboard-reporting' || iconName === 'ai-dashboard'">
      <rect x="3" y="3" width="18" height="18" rx="2" />
      <path d="M7 16v-4M12 16V8M17 16v-6" />
    </template>

    <!-- historian -->
    <template v-else-if="iconName === 'historian'">
      <ellipse cx="12" cy="5" rx="8" ry="3" />
      <path d="M4 5v7c0 1.7 3.6 3 8 3s8-1.3 8-3V5" />
      <path d="M4 12v7c0 1.7 3.6 3 8 3s8-1.3 8-3v-7" />
    </template>

    <!-- edge -->
    <template v-else-if="iconName === 'edge-computing' || iconName === 'edge-ai'">
      <rect x="4" y="4" width="16" height="16" rx="2" />
      <rect x="8" y="8" width="8" height="8" />
      <path d="M2 8h2M2 12h2M2 16h2M20 8h2M20 12h2M20 16h2" />
    </template>

    <!-- mobile -->
    <template v-else-if="iconName === 'mobile-applications'">
      <rect x="7" y="2" width="10" height="20" rx="2" />
      <path d="M10 5h4M11 18h2" />
    </template>

    <!-- =========================================================
         AI
    ========================================================== -->

    <template v-else-if="iconName === 'predictive-maintenance'">
      <path d="M4 18V6M4 18h16" />
      <path d="m7 14 3-3 3 2 5-6" />
      <circle cx="18" cy="7" r="2" />
    </template>

    <template v-else-if="iconName === 'mcb'">
      <rect x="7" y="3" width="10" height="18" rx="2" />
      <path d="M10 7h4M12 7v5l2 2M10 18h4" />
    </template>

    <template v-else-if="iconName === 'computer-vision'">
      <path d="M3 12s3.5-6 9-6 9 6 9 6-3.5 6-9 6-9-6-9-6Z" />
      <circle cx="12" cy="12" r="2.5" />
    </template>

    <template v-else-if="iconName === 'ai-inspection'">
      <circle cx="11" cy="11" r="6" />
      <path d="m16 16 5 5M8 11h6M11 8v6" />
    </template>

    <template v-else-if="iconName === 'ai-analytics'">
      <path d="M4 19V5M4 19h16" />
      <path d="m7 15 4-5 3 3 6-7" />
    </template>

    <template v-else-if="iconName === 'industrial-copilot'">
      <rect x="5" y="5" width="14" height="14" rx="3" />
      <circle cx="9" cy="11" r="1" />
      <circle cx="15" cy="11" r="1" />
      <path d="M9 15h6M12 2v3M12 19v3" />
    </template>

    <template v-else-if="iconName === 'machine-learning'">
      <circle cx="7" cy="7" r="2" />
      <circle cx="17" cy="7" r="2" />
      <circle cx="12" cy="17" r="2" />
      <path d="m8.5 8.5 2 6M15.5 8.5l-2 6M9 7h6" />
    </template>

    <template v-else-if="iconName === 'anomaly-detection'">
      <path d="M3 12h4l2-5 3 10 2-6 2 1h5" />
    </template>

    <!-- =========================================================
         PROCESS
    ========================================================== -->

    <template v-else-if="iconName === 'consultation'">
      <circle cx="8" cy="8" r="3" />
      <circle cx="17" cy="9" r="2.5" />
      <path d="M3 20v-2a5 5 0 0 1 5-5h1a5 5 0 0 1 5 5v2" />
      <path d="M15 14h1a4 4 0 0 1 4 4v2" />
    </template>

    <template v-else-if="iconName === 'engineering'">
      <path d="m14.7 6.3 3-3a4 4 0 0 1-5 5l-6.4 6.4a2 2 0 1 0 3 3l6.4-6.4a4 4 0 0 1 5-5l-3 3" />
      <path d="m5 4 3 3" />
    </template>

    <template v-else-if="iconName === 'discovery'">
      <circle cx="11" cy="11" r="6" />
      <path d="m16 16 5 5M11 8v6M8 11h6" />
    </template>

    <template v-else-if="iconName === 'deployment'">
      <path d="M12 3v12M7 10l5 5 5-5" />
      <path d="M5 21h14" />
    </template>

    <template v-else-if="iconName === 'optimization'">
      <path d="M4 19V5M4 19h16" />
      <path d="m7 15 4-4 3 2 6-7" />
    </template>

    <template v-else-if="iconName === 'monitoring'">
      <path d="M3 12h4l2-6 4 12 2-6h6" />
    </template>

    <!-- =========================================================
         PROCESS AUTOMATION
    ========================================================== -->

    <template v-else-if="iconName === 'dcs'">
      <rect x="4" y="5" width="16" height="14" rx="2" />
      <path d="M8 9h8M8 13h3M14 13h2M8 17h8" />
    </template>

    <template v-else-if="iconName === 'instrumentation'">
      <circle cx="12" cy="12" r="7" />
      <path d="M12 8v4l3 2" />
      <path d="M12 2v3M12 19v3" />
    </template>

    <template v-else-if="iconName === 'process-control'">
      <path d="M3 12h4l2-5 3 10 2-6 2 1h5" />
      <circle cx="18" cy="12" r="2" />
    </template>

    <template v-else-if="iconName === 'batch-control'">
      <rect x="4" y="4" width="16" height="16" rx="2" />
      <path d="M8 8h8M8 12h5M8 16h8" />
    </template>

    <template v-else-if="iconName === 'flow-measurement'">
      <path d="M3 12h18" />
      <path d="m8 7 5 5-5 5M16 7l5 5-5 5" />
    </template>

    <template v-else-if="iconName === 'pressure-measurement'">
      <circle cx="12" cy="13" r="8" />
      <path d="M12 13l4-4M12 5v2" />
    </template>

    <template v-else-if="iconName === 'temperature-measurement'">
      <path d="M12 3a3 3 0 0 0-3 3v7.2a5 5 0 1 0 6 0V6a3 3 0 0 0-3-3Z" />
      <path d="M12 8v7" />
    </template>

    <template v-else-if="iconName === 'level-measurement'">
      <path d="M4 5h16M4 19h16" />
      <path d="M7 8v8M12 8v8M17 8v8" />
    </template>

    <template v-else-if="iconName === 'analyzer-systems'">
      <path d="M5 4h14v16H5z" />
      <path d="M8 8h8M8 12h5M8 16h8" />
    </template>

    <template v-else-if="iconName === 'sis'">
      <path d="M12 2l8 4v6c0 5-3.4 8.5-8 10-4.6-1.5-8-5-8-10V6l8-4Z" />
      <path d="M8 12h8M12 8v8" />
    </template>

    <!-- =========================================================
         CYBERSECURITY
    ========================================================== -->

    <template v-else-if="iconName === 'threat-protection'">
      <path d="M12 2l8 4v6c0 5-3.4 8.5-8 10-4.6-1.5-8-5-8-10V6l8-4Z" />
      <path d="m9 12 2 2 4-4" />
    </template>

    <template v-else-if="iconName === 'network-visibility'">
      <circle cx="6" cy="12" r="2.5" />
      <circle cx="18" cy="6" r="2.5" />
      <circle cx="18" cy="18" r="2.5" />
      <path d="m8.5 11 7-4M8.5 13l7 4" />
    </template>

    <template v-else-if="iconName === 'iec-62443'">
      <path d="M12 2l8 4v6c0 5-3.4 8.5-8 10-4.6-1.5-8-5-8-10V6l8-4Z" />
      <path d="M9 12h6M12 9v6" />
    </template>

    <template v-else-if="iconName === 'network-segmentation'">
      <circle cx="6" cy="6" r="2" />
      <circle cx="18" cy="6" r="2" />
      <circle cx="6" cy="18" r="2" />
      <circle cx="18" cy="18" r="2" />
      <path d="M8 6h8M6 8v8M18 8v8M8 18h8" />
    </template>

    <template v-else-if="iconName === 'firewall'">
      <path d="M4 5h16v14H4z" />
      <path d="M4 9h16M4 13h16M8 5v4M16 9v4M10 13v6M18 13v6" />
    </template>

    <template v-else-if="iconName === 'vpn'">
      <path d="M5 11a7 7 0 0 1 14 0v8H5z" />
      <path d="M9 11V8a3 3 0 0 1 6 0v3M8 15h.01M12 15h.01M16 15h.01" />
    </template>

    <template v-else-if="iconName === 'secure-remote-access'">
      <rect x="5" y="5" width="14" height="14" rx="2" />
      <circle cx="12" cy="11" r="2" />
      <path d="M8 17a4 4 0 0 1 8 0M12 2v3" />
    </template>

    <template v-else-if="iconName === 'ot-assessment'">
      <path d="M5 3h10l4 4v14H5z" />
      <path d="M14 3v5h5M8 12h8M8 16h5" />
    </template>

    <!-- =========================================================
         CYBER PROCESS
    ========================================================== -->

    <template v-else-if="iconName === 'audit'">
      <path d="M5 3h10l4 4v14H5z" />
      <path d="M14 3v5h5M8 12h8M8 16h5" />
      <path d="m15 16 2 2 4-4" />
    </template>

    <template v-else-if="iconName === 'risk-review'">
      <path d="M12 3 3 20h18L12 3Z" />
      <path d="M12 9v5M12 17h.01" />
    </template>

    <template v-else-if="iconName === 'improvement'">
      <path d="M4 19V5M4 19h16" />
      <path d="m7 15 4-4 3 2 6-7" />
      <path d="M16 6h4v4" />
    </template>

    <!-- =========================================================
         SCADA / PLC / REMOTE
    ========================================================== -->

    <template v-else-if="iconName === 'scada'">
      <rect x="3" y="4" width="18" height="14" rx="2" />
      <path d="M7 14l3-3 3 2 4-5M9 21h6M12 18v3" />
    </template>

    <template v-else-if="iconName === 'plc'">
      <rect x="5" y="4" width="14" height="16" rx="2" />
      <path d="M9 8h6M9 12h6M9 16h3" />
      <path d="M2 8h3M2 16h3M19 8h3M19 16h3" />
    </template>

    <template v-else-if="iconName === 'remote'">
      <circle cx="12" cy="12" r="3" />
      <path d="M5 12a7 7 0 0 1 14 0M2 12a10 10 0 0 1 20 0" />
    </template>

    <!-- =========================================================
         FALLBACK
    ========================================================== -->

    <template v-else>
      <path d="M12 20V10M18 20V4M6 20v-4" />
    </template>
  </svg>
</template>

<script setup>
import { computed } from "vue";
import { resolveIconName } from "@/SIS-Dashboard/constants/iconRegistry.js";

const props = defineProps({
  name: {
    type: String,
    required: true,
  },

  size: {
    type: [Number, String],
    default: 20,
  },
});

const formattedSize = computed(() => {
  if (typeof props.size === "number") {
    return `${props.size}px`;
  }
  if (typeof props.size === "string") {
    return props.size.endsWith("px") ||
      props.size.endsWith("rem") ||
      props.size.endsWith("em")
      ? props.size
      : `${props.size}px`;
  }
  return "20px";
});

/*
 * =========================================================
 * ICON ALIASES
 * =========================================================
 *
 * API names that should use an existing semantic icon.
 */
const ICON_ALIASES = {
  // General
  "project-management": "project-management",

  // Digital
  cloud: "cloud-integration",

  // AI
  "ai-powered": "ai",
  "real-time-insights": "insights",

  // Security
  cybersecurity: "security",

  // Existing aliases
  robot: "robot-arm",

  // Infrastructure
  reliability: "quality",
  connected: "connected-enterprise",
  sustainability: "energy",

  "smart-buildings": "building",
  utilities: "server",
  "smart-cities": "globe",
  transportation: "connect",

  assessment: "search",
  strategy: "layers",
  mobility: "connect",
  cities: "globe",
};

/*
 * =========================================================
 * FALLBACK ICONS
 * =========================================================
 *
 * Used only when the API sends an icon name that does not
 * exist in the component.
 *
 * The list is intentionally varied so fallback icons don't
 * all look identical.
 */
const FALLBACK_ICONS = [
  "monitor",
  "layers",
  "energy",
  "database",
  "connect",
  "pulse",
  "chart",
  "robot-arm",
  "cpu",
  "settings",
  "arrow",
  "search",
  "building",
  "server",
  "calendar",
  "globe",
  "factory",
  "science",
  "shield",
  "water",
  "oil",
  "engineering",
];

/*
 * =========================================================
 * KNOWN ICONS
 * =========================================================
 *
 * These are the icons that actually have SVG definitions
 * inside this component.
 */
const KNOWN_ICONS = [
  "monitor",
  "predictive",
  "cube",
  "layers",
  "energy",
  "database",
  "connect",
  "pulse",
  "chart",
  "robot-arm",
  "cpu",
  "settings",
  "arrow",
  "chevron-down",
  "chevron_down",
  "chevron-left",
  "chevron_left",
  "chevron-right",
  "chevron_right",
  "chevron-up",
  "chevron_up",
  "search",
  "menu",
  "close",

  // Industries
  "oil",
  "oil-gas",
  "power-energy",
  "power",
  "water",
  "factory",
  "manufacturing",
  "science",
  "pharmaceutical",
  "chemical",
  "infrastructure",
  "building",

  // Badges
  "quality",
  "expert",
  "support",
  "ai",
  "artificial-intelligence",
  "insights",
  "security",
  "scalable",
  "manufacturing-intelligence",
  "operational-efficiency",
  "connected-enterprise",
  "precision",
  "safety",
  "excellence",

  // Automation
  "integration",
  "testing",
  "commissioning",
  "upgrade",
  "design",
  "documentation",
  "survey",
  "feasibility",
  "project-management",
  "lifecycle",

  // Digital
  "mes",
  "cmms",
  "enterprise-management",
  "iiot",
  "cloud-integration",
  "data-analytics",
  "dashboard-reporting",
  "ai-dashboard",
  "historian",
  "edge-computing",
  "edge-ai",
  "mobile-applications",

  // AI
  "predictive-maintenance",
  "mcb",
  "computer-vision",
  "ai-inspection",
  "ai-analytics",
  "industrial-copilot",
  "machine-learning",
  "anomaly-detection",

  // Process
  "consultation",
  "engineering",
  "discovery",
  "deployment",
  "optimization",
  "monitoring",

  // Process Automation
  "dcs",
  "instrumentation",
  "process-control",
  "batch-control",
  "flow-measurement",
  "pressure-measurement",
  "temperature-measurement",
  "level-measurement",
  "analyzer-systems",
  "sis",

  // Cybersecurity
  "threat-protection",
  "network-visibility",
  "iec-62443",
  "network-segmentation",
  "firewall",
  "vpn",
  "secure-remote-access",
  "ot-assessment",

  // Cyber process
  "audit",
  "risk-review",
  "improvement",

  // SCADA / PLC
  "scada",
  "plc",
  "remote",
];

/*
 * =========================================================
 * DETERMINISTIC FALLBACK
 * =========================================================
 *
 * We DON'T use Math.random().
 *
 * This generates a stable number from the API icon name.
 * So:
 *
 * reliability -> always the same fallback
 * smart-grid  -> always the same fallback
 *
 * But different names normally produce different icons.
 */
const getFallbackIcon = (name) => {
  if (!name) {
    return "settings";
  }

  let hash = 0;

  for (let i = 0; i < name.length; i++) {
    hash = (hash << 5) - hash + name.charCodeAt(i);
    hash |= 0;
  }

  const index = Math.abs(hash) % FALLBACK_ICONS.length;

  return FALLBACK_ICONS[index];
};

/*
 * =========================================================
 * RESOLVE ICON
 * =========================================================
 */
const isSvgIcon = computed(() => {
  const req = (props.name || "").trim().toLowerCase();
  if (!req) return false;
  if (KNOWN_ICONS.includes(req)) return true;
  if (ICON_ALIASES[req] && KNOWN_ICONS.includes(ICON_ALIASES[req])) return true;
  return false;
});

const resolvedMaterialSymbol = computed(() => {
  const req = (props.name || "").trim().toLowerCase();
  if (!req) return "check";
  const resolved = resolveIconName(req) || req;
  return resolved.replace(/-/g, "_");
});

const iconName = computed(() => {
  const requestedName = (props.name || "").trim().toLowerCase();

  // 1. Exact icon exists
  if (KNOWN_ICONS.includes(requestedName)) {
    return requestedName;
  }

  // 2. API alias exists
  if (ICON_ALIASES[requestedName]) {
    return ICON_ALIASES[requestedName];
  }

  // 3. Fallback for SVG
  return "check";
});
</script>
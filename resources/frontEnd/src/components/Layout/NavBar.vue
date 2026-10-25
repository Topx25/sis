<template>
  <header ref="headerEl"
    class="fixed inset-x-0 top-0 z-50 border-b border-[var(--border-20)] bg-[var(--bg-panel-95)] text-sis-text shadow-glow backdrop-blur-xl dark:border-white/10 dark:bg-[#05131f]/95 dark:text-white">
    <!-- =========================================================
         MAIN HEADER
    ========================================================== -->
    <div class="mx-auto flex h-[72px] w-full max-w-[1600px] items-center gap-5 px-4 sm:px-6 lg:px-8 xl:gap-10">
      <!-- =======================================================
           LOGO
      ======================================================== -->
      <router-link :to="{ name: 'home' }" class="flex shrink-0 items-center" aria-label="SiS Holding Home"
        @click="closeAllMenus">
        <img :src="activeLogoUrl" alt="SiS Holding"
          class="h-10 w-auto max-w-[150px] object-contain brightness-100 contrast-100 sm:h-16 sm:max-w-none dark:rounded-md dark:bg-white dark:p-0.5" />
      </router-link>

      <!-- =======================================================
           DESKTOP NAVIGATION
      ======================================================== -->
      <nav class="hidden min-w-0 flex-1 items-center justify-center lg:flex" aria-label="Main navigation">
        <div class="flex h-full items-center justify-center gap-2 lg:gap-5 xl:gap-4">
          <!-- ===================================================
               HOME
          ==================================================== -->
          <router-link
            v-if="homeNav.visible"
            :to="homeNav.url ? homeNav.url : { name: 'home' }"
            class="relative whitespace-nowrap px-3 py-3 text-md font-medium transition-colors duration-200 hover:text-sis-accent"
            :class="isRouteActive(['home']) && !servicesMenuOpen && !industriesMenuOpen
              ? 'font-semibold text-sis-accent'
              : 'text-sis-text dark:text-white/90'
              " @click="closeAllMenus">
            {{ homeNav.label }}

            <span v-if="isRouteActive(['home']) && !servicesMenuOpen && !industriesMenuOpen"
              class="absolute bottom-0 left-1/2 h-[2px] w-6 -translate-x-1/2 rounded-full bg-sis-accent shadow-[0_0_8px_var(--accent-blue)]" />
          </router-link>

          <!-- ===================================================
               ABOUT US
          ==================================================== -->
          <router-link
            v-if="aboutNav.visible"
            :to="aboutNav.url ? aboutNav.url : { name: 'about' }"
            class="relative whitespace-nowrap px-3 py-3 text-md font-medium transition-colors duration-200 hover:text-sis-accent"
            :class="isRouteActive(['about']) && !servicesMenuOpen && !industriesMenuOpen
              ? 'font-semibold text-sis-accent'
              : 'text-sis-text dark:text-white/90'
              " @click="closeAllMenus">
            {{ aboutNav.label }}

            <span v-if="isRouteActive(['about']) && !servicesMenuOpen && !industriesMenuOpen"
              class="absolute bottom-0 left-1/2 h-[2px] w-6 -translate-x-1/2 rounded-full bg-sis-accent shadow-[0_0_8px_var(--accent-blue)]" />
          </router-link>

          <!-- ===================================================
               SERVICES & SOLUTIONS
          ==================================================== -->
          <div v-if="servicesNav.visible" class="relative h-full">
            <button type="button"
              class="relative flex h-full items-center gap-1.5 whitespace-nowrap px-3 text-md font-medium transition-colors duration-200 hover:text-sis-accent"
              :class="servicesMenuOpen ||
                isRouteActive(['services'])
                ? 'font-semibold text-sis-accent'
                : 'text-sis-text dark:text-white/90'
                " @click="toggleServicesMenu">
              <span>{{ servicesNav.label }}</span>

              <UiIcon name="chevron-down" :size="13" class="transition-transform duration-200"
                :class="servicesMenuOpen ? 'rotate-180' : ''" />

              <!-- Active underline -->
              <span v-if="
                servicesMenuOpen ||
                isRouteActive(['services'])
              "
                class="absolute bottom-[-12px] left-1/2 h-[2px] w-9 -translate-x-1/2 rounded-full bg-sis-accent shadow-[0_0_8px_var(--accent-blue)]" />
            </button>

            <!-- =================================================
                MEGA MENU
            ================================================== -->
            <transition name="mega-menu">
              <div v-if="servicesMenuOpen" class="fixed left-0 right-0 top-[72px] z-[100]">
                <!-- Full width background with soft gradient -->
                <div
                  class="w-full border-b border-white/10 bg-gradient-to-b from-[#03131f] via-[#041825] to-[#03131f] shadow-[0_30px_80px_rgba(0,0,0,0.55)] backdrop-blur-xl">
                  <div class="mx-auto w-full max-w-[1440px] px-4 py-6 sm:px-6 lg:px-10 xl:px-12">
                    <div class="grid grid-cols-1 gap-6 lg:grid-cols-[1fr_320px] lg:gap-8">

                      <!-- ===================== SERVICES GRID ===================== -->
                      <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-3">
                        <button v-for="service in servicesMenu" :key="service.slug || service.id" type="button"
                          class="group relative flex items-start gap-4 rounded-2xl border border-white/5 bg-white/[0.03] p-4 text-left transition-all duration-300 hover:border-transparent hover:bg-white/[0.06] hover:shadow-lg hover:shadow-blue-500/10"
                          @click="handleServiceClick(service)">
                          <!-- Gradient border on hover -->
                          <div
                            class="absolute inset-0 rounded-2xl bg-gradient-to-br from-blue-500 to-purple-600 opacity-0 transition-opacity duration-300 group-hover:opacity-100 -z-10">
                          </div>
                          <div
                            class="absolute inset-[1px] rounded-[15px] bg-[#03131f] group-hover:bg-[#041a2a] transition-colors -z-10">
                          </div>

                          <!-- Icon -->
                          <div
                            class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-blue-500/15 to-purple-600/15 text-blue-400 transition-all duration-300 group-hover:from-blue-500 group-hover:to-purple-600 group-hover:text-white group-hover:shadow-md group-hover:shadow-blue-500/30">
                            <UiIcon :name="service.icon || 'settings'" :size="22" />
                          </div>

                          <!-- Text -->
                          <div class="min-w-0 flex-1 pt-0.5">
                            <div class="flex items-center gap-2">
                              <h3
                                class="text-[14px] font-semibold leading-tight text-white transition-colors group-hover:text-transparent group-hover:bg-clip-text group-hover:bg-gradient-to-r group-hover:from-blue-400 group-hover:to-purple-400">
                                {{ service.title }}
                              </h3>
                              <span
                                class="text-blue-400 opacity-0 -translate-x-1 transition-all duration-300 group-hover:opacity-100 group-hover:translate-x-0">
                                →
                              </span>
                            </div>

                            <p v-if="service.description" class="mt-1.5 text-[12px] leading-relaxed text-slate-400 line-clamp-2">
                              {{ service.description }}
                            </p>
                          </div>
                        </button>
                      </div>

                      <!-- ===================== PROMO CARD ===================== -->
                      <div class="lg:pl-2">
                        <div
                          class="relative h-full min-h-[380px] overflow-hidden rounded-2xl border border-white/10 bg-[#061a2a] shadow-xl">
                          <!-- Promo image -->
                          <img :src="servicesPromoImage" alt="Smart Solutions"
                            class="absolute inset-0 h-full w-full object-cover transition-transform duration-700 group-hover:scale-105" />

                          <!-- Strong gradient overlay -->
                          <div class="absolute inset-0 bg-gradient-to-t from-[#020b13] via-[#03131f]/85 to-blue-900/30">
                          </div>

                          <!-- Subtle top accent line -->
                          <div
                            class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-blue-500 via-purple-500 to-blue-500">
                          </div>

                          <!-- Promo content -->
                          <div class="relative z-10 flex h-full flex-col justify-end p-6">
                            <span
                              class="inline-flex w-fit items-center rounded-full bg-gradient-to-r from-blue-500/20 to-purple-600/20 px-3 py-1 text-[10px] font-bold uppercase tracking-[0.18em] text-blue-300 border border-blue-400/20">
                              Smart Solutions
                            </span>

                            <h3 class="mt-4 text-[22px] font-bold leading-tight text-white">
                              Solutions Built for
                              <br />
                              <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-purple-400">
                                Performance & Growth
                              </span>
                            </h3>

                            <p class="mt-3 text-[13px] leading-relaxed text-slate-300 max-w-[280px]">
                              Smart, scalable, and secure solutions to optimize operations and drive business value
                              across your organization.
                            </p>

                            <button type="button"
                              class="mt-5 flex w-fit items-center gap-2 rounded-full bg-gradient-to-r from-blue-600 to-purple-600 px-5 py-2.5 text-[13px] font-semibold text-white shadow-lg shadow-blue-500/25 transition-all hover:shadow-xl hover:shadow-purple-500/30 hover:scale-[1.03]"
                              @click="handleSolutionsClick">
                              <span>Explore Solutions</span>
                              <span aria-hidden="true">→</span>
                            </button>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </transition>
          </div>

          <!-- ===================================================
               INDUSTRIES
          ==================================================== -->
          <div v-if="industriesNav.visible" class="relative h-full">
            <!-- ===================== TRIGGER BUTTON ===================== -->
            <button type="button"
              class="relative flex h-full items-center gap-1.5 whitespace-nowrap px-3 text-md font-medium transition-all duration-200"
              :class="industriesMenuOpen || isRouteActive(['projects'])
                  ? 'font-semibold text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-purple-400'
                  : 'text-sis-text dark:text-white/90 hover:text-transparent hover:bg-clip-text hover:bg-gradient-to-r hover:from-blue-400 hover:to-purple-400'
                " @click="toggleIndustriesMenu">
              <span>{{ industriesNav.label }}</span>

              <UiIcon name="chevron-down" :size="13" class="transition-transform duration-200"
                :class="industriesMenuOpen ? 'rotate-180 text-blue-400' : ''" />

              <!-- Active underline with gradient -->
              <span v-if="industriesMenuOpen || isRouteActive(['projects'])"
                class="absolute bottom-[-12px] left-1/2 h-[2.5px] w-10 -translate-x-1/2 rounded-full bg-gradient-to-r from-blue-500 to-purple-500 shadow-[0_0_12px_rgba(59,130,246,0.5)]" />
            </button>

            <!-- ===================== MEGA MENU ===================== -->
            <transition name="mega-menu">
              <div v-if="industriesMenuOpen" class="fixed left-0 right-0 top-[72px] z-[100]">
                <div
                  class="w-full border-b border-white/10 bg-gradient-to-b from-[#03131f] via-[#041825] to-[#03131f] shadow-[0_30px_80px_rgba(0,0,0,0.55)] backdrop-blur-xl">
                  <div class="mx-auto w-full max-w-[1440px] px-4 py-6 sm:px-6 lg:px-10 xl:px-12">
                    <div class="grid grid-cols-1 gap-6 lg:grid-cols-[1fr_340px] lg:gap-8">

                      <!-- ===================== INDUSTRIES GRID ===================== -->
                      <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
                        <button v-for="industry in industriesMenu" :key="industry.slug" type="button"
                          class="group relative overflow-hidden rounded-2xl border border-white/5 bg-white/[0.03] text-left transition-all duration-300 hover:border-transparent hover:shadow-xl hover:shadow-blue-500/10"
                          @click="handleIndustriesClick(industry)">
                          <!-- Gradient border on hover -->
                          <div
                            class="absolute inset-0 rounded-2xl bg-gradient-to-br from-blue-500 to-purple-600 opacity-0 transition-opacity duration-300 group-hover:opacity-100 -z-10">
                          </div>
                          <div
                            class="absolute inset-[1px] rounded-[15px] bg-[#03131f] group-hover:bg-[#041a2a] transition-colors -z-10">
                          </div>

                          <!-- Image -->
                          <div class="relative h-[130px] w-full overflow-hidden">
                            <img :src="industry.image" :alt="industry.title"
                              class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-110" />
                            <div
                              class="absolute inset-0 bg-gradient-to-t from-[#03131f] via-[#03131f]/40 to-transparent">
                            </div>
                          </div>

                          <!-- Content -->
                          <div class="p-4">
                            <div class="flex items-center justify-between">
                              <div
                                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-blue-500/15 to-purple-600/15 text-blue-400 transition-all duration-300 group-hover:from-blue-500 group-hover:to-purple-600 group-hover:text-white group-hover:shadow-md group-hover:shadow-blue-500/30">
                                <UiIcon :name="industry.icon" :size="18" />
                              </div>

                              <span
                                class="text-blue-400 opacity-0 -translate-x-1 transition-all duration-300 group-hover:opacity-100 group-hover:translate-x-0">
                                →
                              </span>
                            </div>

                            <h3
                              class="mt-3 text-[15px] font-semibold leading-tight text-white transition-all group-hover:text-transparent group-hover:bg-clip-text group-hover:bg-gradient-to-r group-hover:from-blue-400 group-hover:to-purple-400">
                              {{ industry.title }}
                            </h3>

                            <p class="mt-1.5 text-[12px] leading-relaxed text-slate-400 line-clamp-2">
                              {{ industry.description }}
                            </p>
                          </div>
                        </button>
                      </div>

                      <!-- ===================== PROMO CARD ===================== -->
                      <div class="lg:pl-2">
                        <div
                          class="relative h-full min-h-[420px] overflow-hidden rounded-2xl border border-white/10 bg-[#061a2a] shadow-xl">
                          <img :src="industriesPromoImage" alt="Industry expertise"
                            class="absolute inset-0 h-full w-full object-cover" />

                          <!-- Strong gradient overlay -->
                          <div class="absolute inset-0 bg-gradient-to-t from-[#020b13] via-[#03131f]/85 to-blue-900/30">
                          </div>

                          <!-- Top accent line -->
                          <div
                            class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-blue-500 via-purple-500 to-blue-500">
                          </div>

                          <div class="relative z-10 flex h-full flex-col justify-end p-6">
                            <span
                              class="inline-flex w-fit items-center rounded-full bg-gradient-to-r from-blue-500/20 to-purple-600/20 px-3 py-1 text-[10px] font-bold uppercase tracking-[0.18em] text-blue-300 border border-blue-400/20">
                              Industry Expertise
                            </span>

                            <h3 class="mt-4 text-[24px] font-bold leading-tight text-white">
                              Solutions Tailored
                              <br />
                              <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-purple-400">
                                for Every Industry
                              </span>
                            </h3>

                            <p class="mt-3 text-[13px] leading-relaxed text-slate-300 max-w-[280px]">
                              Deep domain expertise and proven experience across a wide range of industries to deliver
                              measurable results and long-term value.
                            </p>

                            <button type="button"
                              class="mt-5 flex w-fit items-center gap-2 rounded-full bg-gradient-to-r from-blue-600 to-purple-600 px-5 py-2.5 text-[13px] font-semibold text-white shadow-lg shadow-blue-500/25 transition-all hover:shadow-xl hover:shadow-purple-500/30 hover:scale-[1.03]"
                              @click="handleIndustryPromoClick">
                              <span>Explore Industries</span>
                              <span aria-hidden="true">→</span>
                            </button>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </transition>
          </div>

          <!-- ===================================================
               PARTNER ECOSYSTEM
          ==================================================== -->
          <router-link
            v-if="partnersNav.visible"
            :to="partnersNav.url ? partnersNav.url : { name: 'partner-ecosystem' }"
            class="relative whitespace-nowrap px-3 py-3 text-md font-medium transition-colors duration-200 hover:text-sis-accent"
            :class="isRouteActive(['partner-ecosystem'])
              ? 'font-semibold text-sis-accent'
              : 'text-sis-text dark:text-white/90'
              " @click="closeAllMenus">
            {{ partnersNav.label }}

            <span v-if="isRouteActive(['partner-ecosystem'])"
              class="absolute bottom-0 left-1/2 h-[2px] w-6 -translate-x-1/2 rounded-full bg-sis-accent shadow-[0_0_8px_var(--accent-blue)]" />
          </router-link>

          <!-- ===================================================
               USE CASES
          ==================================================== -->
          <button
            v-if="useCasesNav.visible"
            type="button"
            class="relative whitespace-nowrap px-3 py-3 text-md font-medium transition-colors duration-200 hover:text-sis-accent"
            :class="isRouteActive(['use-cases'])
              ? 'font-semibold text-sis-accent'
              : 'text-sis-text dark:text-white/90'
              " @click="handleUseCases">
            {{ useCasesNav.label }}

            <span v-if="isRouteActive(['use-cases'])"
              class="absolute bottom-0 left-1/2 h-[2px] w-6 -translate-x-1/2 rounded-full bg-sis-accent shadow-[0_0_8px_var(--accent-blue)]" />
          </button>
        </div>
      </nav>

      <!-- =======================================================
           DESKTOP ACTIONS
      ======================================================== -->
      <div class="hidden shrink-0 items-center gap-3 lg:flex lg:gap-4">

        <!-- Language -->
        <!-- <button type="button"
          class="flex h-9 min-w-9 items-center justify-center rounded-full border border-sis-border px-2.5 text-sm font-semibold text-sis-text transition-colors hover:border-sis-accent hover:text-sis-accent dark:border-white/10 dark:text-white/80"
          :aria-label="currentLanguage === 'en' ? 'Switch to Arabic' : 'Switch to English'" @click="toggleLanguage">
          {{ currentLanguage === "en" ? "AR" : "EN" }}
        </button> -->



        <!-- CTA Button -->
        <router-link
          v-if="ctaButton.enabled"
          :to="ctaButton.link"
          class="group flex shrink-0 items-center gap-2 rounded-full border border-sis-accent px-5 py-2 text-md font-semibold text-white bg-sis-accent transition-all duration-200 hover:bg-blue-700"
          @click="closeAllMenus">
          <span>{{ ctaButton.text }}</span>

          <span aria-hidden="true"
            class="text-base leading-none transition-transform duration-200 group-hover:translate-x-0.5">
            →
          </span>
        </router-link>
      </div>

      <!-- =======================================================
           MOBILE CONTROLS
      ======================================================== -->
      <div class="ml-auto flex items-center gap-2 lg:hidden">




        <!-- Menu button -->
        <button type="button"
          class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-sis-border text-sis-text transition-all duration-200 hover:border-sis-accent hover:text-sis-accent active:scale-95 dark:border-white/10 dark:text-white"
          :aria-expanded="menuOpen" aria-controls="mobile-navigation"
          :aria-label="menuOpen ? 'Close navigation menu' : 'Open navigation menu'" @click="toggleMenu">
          <UiIcon :name="menuOpen ? 'close' : 'menu'" :size="20" />
        </button>
      </div>
    </div>

    <!-- =========================================================
         MOBILE MENU
    ========================================================== -->
    <transition name="content-fade">
      <div v-if="menuOpen" id="mobile-navigation"
        class="border-t border-white/10 bg-[#061826]/98 px-4 py-5 text-white lg:hidden">
        <nav class="flex flex-col gap-1" aria-label="Mobile navigation">
          <!-- Home -->
          <router-link
            v-if="homeNav.visible"
            :to="homeNav.url ? homeNav.url : { name: 'home' }"
            class="rounded-xl px-4 py-3 text-md font-medium text-black/90 transition hover:bg-[var(--bg-accent-10)] hover:text-sis-accent"
            :class="isRouteActive(['home'])
              ? 'bg-[var(--bg-accent-10)] font-semibold text-sis-accent'
              : ''
              " @click="closeAllMenus">
            {{ homeNav.label }}
          </router-link>

          <!-- About Us -->
          <router-link
            v-if="aboutNav.visible"
            :to="aboutNav.url ? aboutNav.url : { name: 'about' }"
            class="rounded-xl px-4 py-3 text-md font-medium text-black/90 transition hover:bg-[var(--bg-accent-10)] hover:text-sis-accent"
            :class="isRouteActive(['about'])
              ? 'bg-[var(--bg-accent-10)] font-semibold text-sis-accent'
              : ''
              " @click="closeAllMenus">
            {{ aboutNav.label }}
          </router-link>

          <!-- Services & Solutions -->
          <div v-if="servicesNav.visible">
            <button type="button"
              class="flex w-full items-center justify-between rounded-xl px-4 py-3 text-left text-md font-medium text-black/90 transition hover:bg-[var(--bg-accent-10)] hover:text-sis-accent"
              :aria-expanded="mobileOpenMenu === 'services'" aria-controls="mobile-services-menu" :class="isRouteActive(['services', 'architecture'])
                ? 'font-semibold text-sis-accent'
                : ''
                " @click="toggleMobileSubmenu('services')">
              <span>{{ servicesNav.label }}</span>

              <UiIcon name="chevron-down" :size="16" class="transition-transform duration-200" :class="mobileOpenMenu === 'services'
                ? 'rotate-180 text-sis-accent'
                : ''
                " />
            </button>

            <transition name="submenu">
              <div v-if="mobileOpenMenu === 'services'" id="mobile-services-menu"
                class="mx-2 mb-1 overflow-hidden rounded-xl bg-[var(--bg-accent-10)]">
                <button v-for="service in servicesMenu" :key="service.slug || service.id" type="button"
                  class="flex w-full items-center gap-3 px-5 py-3 text-left text-md text-black/50 transition hover:bg-white/5 hover:text-sis-accent"
                  @click="handleServiceClick(service)">
                  <UiIcon :name="service.icon || 'settings'" :size="18" class="shrink-0 text-sis-accent" />

                  <span>
                    {{ service.title }}
                  </span>
                </button>
              </div>
            </transition>
          </div>

          <!-- Industries -->
          <div v-if="industriesNav.visible">
            <button type="button"
              class="flex w-full items-center justify-between rounded-xl px-4 py-3 text-left text-md font-medium text-black/90 transition hover:bg-[var(--bg-accent-10)] hover:text-sis-accent"
              :aria-expanded="mobileOpenMenu === 'industries'" aria-controls="mobile-industries-menu" :class="isRouteActive(['projects'])
                ? 'font-semibold text-sis-accent'
                : ''
                " @click="toggleMobileSubmenu('industries')">
              <span>{{ industriesNav.label }}</span>

              <UiIcon name="chevron-down" :size="16" class="transition-transform duration-200" :class="mobileOpenMenu === 'industries'
                ? 'rotate-180 text-sis-accent'
                : ''
                " />
            </button>

            <transition name="submenu">
              <div v-if="mobileOpenMenu === 'industries'" id="mobile-industries-menu"
                class="mx-2 mb-1 overflow-hidden rounded-xl bg-[var(--bg-accent-10)]">
                <button v-for="industry in industriesMenu" :key="industry.slug" type="button"
                  class="flex w-full items-center gap-3 px-5 py-3 text-left text-md text-black/50 transition hover:bg-white/5 hover:text-sis-accent"
                  @click="handleIndustriesClick(industry)">
                  <UiIcon :name="industry.icon" :size="18" class="shrink-0 text-sis-accent" />

                  <span>{{ industry.title }}</span>
                </button>
              </div>
            </transition>
          </div>

          <!-- Partner Ecosystem -->
          <router-link
            v-if="partnersNav.visible"
            :to="partnersNav.url ? partnersNav.url : { name: 'partner-ecosystem' }"
            class="rounded-xl px-4 py-3 text-md font-medium text-black/90 transition hover:bg-[var(--bg-accent-10)] hover:text-sis-accent"
            :class="isRouteActive(['partner-ecosystem'])
              ? 'bg-[var(--bg-accent-10)] font-semibold text-sis-accent'
              : ''
              " @click="closeAllMenus">
            {{ partnersNav.label }}
          </router-link>

          <!-- Use Cases -->
          <button
            v-if="useCasesNav.visible"
            type="button"
            class="rounded-xl px-4 py-3 text-left text-md font-medium text-black/90 transition hover:bg-[var(--bg-accent-10)] hover:text-sis-accent"
            :class="isRouteActive(['use-cases'])
              ? 'bg-[var(--bg-accent-10)] font-semibold text-sis-accent'
              : ''
              " @click="handleUseCases">
            {{ useCasesNav.label }}
          </button>


          <!-- Mobile CTA -->
          <router-link
            v-if="ctaButton.enabled"
            :to="ctaButton.link"
            class="mt-3 flex w-full items-center justify-center gap-2 rounded-full border border-sis-accent py-3 text-md font-semibold text-sis-accent transition hover:bg-sis-accent hover:text-black"
            @click="closeAllMenus">
            <span>{{ ctaButton.text }}</span>

            <span aria-hidden="true">→</span>
          </router-link>
        </nav>
      </div>
    </transition>
  </header>
</template>

<script setup>
import { computed, ref, watch, onMounted, onBeforeUnmount } from "vue";
import { useRoute, useRouter } from "vue-router";
import { api } from "@/api/index.js";
import { resolveImageUrl } from "@/utils/imageUrl";
import { useSiteStore } from "@/stores/useSiteStore";

const siteStore = useSiteStore();

import UiIcon from "@/components/common/UiIcon.vue";

/*
|--------------------------------------------------------------------------
| Router
|--------------------------------------------------------------------------
*/

const route = useRoute();
const router = useRouter();

/*
|--------------------------------------------------------------------------
| Refs
|--------------------------------------------------------------------------
*/

const headerEl = ref(null);

const menuOpen = ref(false);

const mobileOpenMenu = ref(null);

const servicesMenuOpen = ref(false);

const industriesMenuOpen = ref(false);
let previousBodyOverflow = "";

watch([menuOpen, servicesMenuOpen, industriesMenuOpen], ([isMobileMenuOpen, isServicesMenuOpen, isIndustriesMenuOpen]) => {
  const isAnyMenuOpen = isMobileMenuOpen || isServicesMenuOpen || isIndustriesMenuOpen;

  document.body.style.overflow = isAnyMenuOpen ? "hidden" : previousBodyOverflow;
});

const handleClickOutside = (event) => {
  if (!headerEl.value?.contains(event.target)) {
    closeAllMenus();
  }
};

onMounted(async () => {
  previousBodyOverflow = document.body.style.overflow;
  document.addEventListener("click", handleClickOutside);

  fetchSolutionPagesMenu();
  siteStore.loadHeader().catch(() => {});

  try {
    const res = await api.getIndustries();
    const payload = res?.data || res;
    if (payload?.hero?.image_url || payload?.hero?.image) {
      industriesPromoImage.value = resolveImageUrl(payload.hero.image_url || payload.hero.image);
    }
    const rawList = payload?.industries || payload?.sectors;
    if (Array.isArray(rawList) && rawList.length > 0) {
      industriesMenu.value = rawList.map((item) => {
        const rawImg = item.image_url || item.image;
        const img =
          rawImg && typeof rawImg === "string" && !rawImg.includes("undefined")
            ? resolveImageUrl(rawImg)
            : getIndustryFallbackImage(item.slug, item.name);
        return {
          id: item.id,
          title: item.name || item.title,
          slug: item.slug,
          icon: item.icon || "factory",
          description: item.description || "",
          image: img,
          projects_count: item.projects_count,
          relevant_programs: item.relevant_programs || [],
          typical_scope: item.typical_scope || [],
        };
      });
    }
  } catch (err) {
    // Keep defaults on fallback
  }
});

onBeforeUnmount(() => {
  document.body.style.overflow = previousBodyOverflow;
  document.removeEventListener("click", handleClickOutside);
});

/*
|--------------------------------------------------------------------------
| Assets & Dynamic Header Data
|--------------------------------------------------------------------------
*/

const sisLogoUrl = `${import.meta.env.BASE_URL}images/sis-panel-img.jpeg`;

const activeLogoUrl = computed(() => {
  const custom = siteStore.header?.logo_url || siteStore.header?.logo_path;
  return custom ? resolveImageUrl(custom) : sisLogoUrl;
});

const navItem = (key, fallbackLabel) => {
  if (siteStore.header?.nav_links && Array.isArray(siteStore.header.nav_links)) {
    const found = siteStore.header.nav_links.find((l) => l.key === key);
    if (found) {
      return {
        label: found.label || fallbackLabel,
        url: found.url || null,
        visible: found.is_active !== false,
      };
    }
  }
  return { label: fallbackLabel, url: null, visible: true };
};

const homeNav = computed(() => navItem("home", "Home"));
const aboutNav = computed(() => navItem("about", "About Us"));
const servicesNav = computed(() => navItem("services", "Services & Solutions"));
const industriesNav = computed(() => navItem("industries", "Industries"));
const partnersNav = computed(() => navItem("partner-ecosystem", "Partner Ecosystem"));
const useCasesNav = computed(() => navItem("use-cases", "Use Cases"));

const ctaButton = computed(() => ({
  enabled: siteStore.header?.button_enabled !== false,
  text: siteStore.header?.button_text || "Get in Touch",
  link: siteStore.header?.button_link || "/contact",
}));

const servicesPromoImage =
  `${import.meta.env.BASE_URL}images/page111.webp`;

const industriesPromoImage = ref(
  `${import.meta.env.BASE_URL}images/industry-heavy.webp`
);

const currentLanguage = ref(
  localStorage.getItem("language") || "en"
);

const toggleLanguage = () => {
  currentLanguage.value =
    currentLanguage.value === "en" ? "ar" : "en";

  localStorage.setItem("language", currentLanguage.value);

  window.location.reload();
};

/*
|--------------------------------------------------------------------------
| Temporary Canonical Fallback Catalog
| (Used strictly when the backend endpoint fails or returns an invalid/empty response)
|--------------------------------------------------------------------------
*/
const CANONICAL_SERVICES_FALLBACK = [
  {
    title: "Digital Solutions",
    slug: "digital-solutions",
    icon: "monitor",
    description: "Digital transformation, IIoT, cloud, analytics and enterprise digital platforms.",
  },
  {
    title: "Engineering Services",
    slug: "engineering-services",
    icon: "engineering",
    description: "Engineering design, planning, documentation and project engineering services.",
  },
  {
    title: "General Services",
    slug: "general-services",
    icon: "settings",
    description: "Complete engineering and lifecycle services from concept to commissioning.",
  },
  {
    title: "Industrial AI",
    slug: "industrial-ai",
    icon: "cpu",
    description: "AI-powered solutions for intelligent operations, vision systems and data-driven decisions.",
  },
  {
    title: "Industrial Automation",
    slug: "industrial-automation",
    icon: "factory",
    description: "Automation solutions for manufacturing, machines and production lines.",
  },
  {
    title: "Infrastructure Solutions",
    slug: "infrastructure-solutions",
    icon: "factory",
    description: "Smart infrastructure solutions for buildings, utilities, transportation and critical facilities.",
  },
  {
    title: "Operations & Maintenance",
    slug: "operations-maintenance",
    icon: "settings",
    description: "Operations support, maintenance services and AMC programs.",
  },
  {
    title: "OT Cybersecurity",
    slug: "ot-cybersecurity",
    icon: "shield",
    description: "Secure industrial systems and networks with advanced cybersecurity solutions.",
  },
  {
    title: "Predictive Maintenance",
    slug: "predictive-maintenance",
    icon: "predictive",
    description: "Condition monitoring and predictive analytics to maximize asset reliability and performance.",
  },
  {
    title: "Process Automation",
    slug: "process-automation",
    icon: "cpu",
    description: "Process control and optimization solutions for continuous industries.",
  },
  {
    title: "Smart Manufacturing",
    slug: "smart-manufacturing",
    icon: "connect",
    description: "Industry 4.0 solutions for smart factories, MES and manufacturing excellence.",
  },
  {
    title: "Training & Consulting",
    slug: "training-consulting",
    icon: "science",
    description: "Technical training, certifications and consulting to build capabilities.",
  },
];

/*
|--------------------------------------------------------------------------
| Services Mega Menu Data (API-driven with isolated fallback)
|--------------------------------------------------------------------------
*/
const servicesMenu = ref([...CANONICAL_SERVICES_FALLBACK]);
const servicesMenuLoading = ref(false);

const fetchSolutionPagesMenu = async () => {
  servicesMenuLoading.value = true;
  try {
    const res = await api.getSolutionPagesMenu();
    let rawList = [];
    if (Array.isArray(res)) {
      rawList = res;
    } else if (Array.isArray(res?.data)) {
      rawList = res.data;
    } else if (res && typeof res === "object" && !res.__PHP_Incomplete_Class_Name) {
      const values = Object.values(res).filter(
        (item) => item && typeof item === "object" && Boolean(item.slug || item.title || item.name)
      );
      if (values.length > 0) rawList = values;
    }

    const validApiItems = rawList
      .filter((item) => item && typeof item === "object" && Boolean(item.slug))
      .map((item) => ({
        id: item.id,
        title: (typeof item.title === "object" ? item.title?.en : item.title) || item.name || "",
        slug: item.slug || "",
        icon: item.icon || "settings",
        description: (typeof item.description === "object" ? item.description?.en : item.description) || "",
      }));

    if (validApiItems.length > 0) {
      // API returned valid non-empty records: render API data in exact order
      servicesMenu.value = validApiItems;
    } else {
      // API returned empty or invalid/corrupted payload: engage temporary canonical fallback
      console.warn(
        "[NavBar] /solution-pages/menu returned invalid or empty data. Engaging temporary canonical fallback.",
        res
      );
      servicesMenu.value = [...CANONICAL_SERVICES_FALLBACK];
    }
  } catch (err) {
    console.error(
      "[NavBar] Failed to fetch /solution-pages/menu. Engaging temporary canonical fallback:",
      err
    );
    servicesMenu.value = [...CANONICAL_SERVICES_FALLBACK];
  } finally {
    servicesMenuLoading.value = false;
  }
};

function getIndustryFallbackImage(slug = "", title = "") {
  const s = (slug || "").toLowerCase();
  const t = (title || "").toLowerCase();
  if (s.includes("oil") || s.includes("gas") || t.includes("oil") || t.includes("gas")) {
    return `${import.meta.env.BASE_URL}images/industry-oil-gas.webp`;
  }
  if (s.includes("water") || t.includes("water")) {
    return `${import.meta.env.BASE_URL}images/industry-water.webp`;
  }
  if (s.includes("chem") || t.includes("chem")) {
    return `${import.meta.env.BASE_URL}images/industry-chemical.webp`;
  }
  if (s.includes("food") || s.includes("beverage") || s.includes("fmcg") || t.includes("food") || t.includes("beverage")) {
    return `${import.meta.env.BASE_URL}images/industry-food.webp`;
  }
  if (s.includes("heavy") || t.includes("heavy") || s.includes("steel") || s.includes("foundr") || s.includes("cement")) {
    return `${import.meta.env.BASE_URL}images/industry-heavy.webp`;
  }
  if (s.includes("energy") || s.includes("utilit") || s.includes("power")) {
    return `${import.meta.env.BASE_URL}images/industry-heavy.webp`;
  }
  return `${import.meta.env.BASE_URL}images/factory.webp`;
}

const defaultIndustriesMenu = [
  {
    title: "Oil & Gas",
    slug: "oil-gas",
    icon: "oil",
    description: "Upstream, midstream, and downstream digital oilfield instrumentation and safety systems.",
    image: `${import.meta.env.BASE_URL}images/industry-oil-gas.webp`,
  },
  {
    title: "Water & Waste Water",
    slug: "water-waste-water",
    icon: "water",
    description: "Desalination plants, wastewater treatment, and regional water distribution automation.",
    image: `${import.meta.env.BASE_URL}images/industry-water.webp`,
  },
  {
    title: "Chemical Industries",
    slug: "chemical-industries",
    icon: "science",
    description: "Chemical process optimization, safety valve control, and hazardous environment automation.",
    image: `${import.meta.env.BASE_URL}images/industry-chemical.webp`,
  },
  {
    title: "Food, Beverage & FMCG",
    slug: "food-beverage-fmcg",
    icon: "science",
    description: "High-speed packaging, chemical mixing, ERP traceability, and thermal process control.",
    image: `${import.meta.env.BASE_URL}images/industry-food.webp`,
  },
  {
    title: "Heavy Industries",
    slug: "heavy-industries",
    icon: "factory",
    description: "Cement manufacturing, steel foundries, kiln control, and conveyor systems.",
    image: `${import.meta.env.BASE_URL}images/industry-heavy.webp`,
  },
];

const industriesMenu = ref([...defaultIndustriesMenu]);

/*
|--------------------------------------------------------------------------
| Route Helpers
|--------------------------------------------------------------------------
*/

const isRouteActive = (routes) => {
  return routes.includes(route.name);
};

/*
|--------------------------------------------------------------------------
| Services Mega Menu
|--------------------------------------------------------------------------
*/

const openServicesMenu = () => {
  industriesMenuOpen.value = false;
  servicesMenuOpen.value = true;
};

const closeServicesMenu = () => {
  servicesMenuOpen.value = false;
};

const toggleServicesMenu = () => {
  if (servicesMenuOpen.value) {
    servicesMenuOpen.value = false;
  } else {
    industriesMenuOpen.value = false;
    servicesMenuOpen.value = true;
  }
};


const openIndustriesMenu = () => {
  servicesMenuOpen.value = false;

  industriesMenuOpen.value = true;
  router.push({ name: 'projects' });
};

const closeIndustriesMenu = () => {
  industriesMenuOpen.value = false;
};

const toggleIndustriesMenu = () => {
  if (industriesMenuOpen.value) {
    industriesMenuOpen.value = false;
  } else {
    servicesMenuOpen.value = false;
    industriesMenuOpen.value = true;
  }
};

const handleIndustriesMouseLeave = () => {
  /*
   * We intentionally don't immediately close the menu here.
   * The fixed mega menu itself handles mouseleave.
   */
};

/*
|--------------------------------------------------------------------------
| Service Click
|--------------------------------------------------------------------------
*/

const handleServiceClick = (service) => {
  if (!service?.slug) return;
  router.push({
    name: "solution-page",
    params: {
      slug: service.slug,
    },
  });

  closeAllMenus();
};


const handleIndustriesClick = (industry) => {
  console.log("Selected industry:", industry?.slug);

  router.push({ name: "projects" });
  closeAllMenus();
};

/*
|--------------------------------------------------------------------------
| Explore Solutions
|--------------------------------------------------------------------------
*/

const handleSolutionsClick = () => {
  /*
   * The real Solutions route will be added later.
   */

  router.push("/contact")

  closeAllMenus();
};

const handleIndustryClick = (industry) => {
  console.log("Selected industry:", industry.slug);

  closeAllMenus();
};

const handleIndustryPromoClick = () => {
  router.push({ name: 'projects' });

  closeAllMenus();
};

/*
|--------------------------------------------------------------------------
| Use Cases
|--------------------------------------------------------------------------
*/

const handleUseCases = () => {
  closeAllMenus();

  router.push({
    name: "use-cases",
  });
};

/*
|--------------------------------------------------------------------------
| Mobile Menu
|--------------------------------------------------------------------------
*/

const toggleMenu = () => {
  menuOpen.value = !menuOpen.value;

  if (!menuOpen.value) {
    mobileOpenMenu.value = null;
  }
};

const toggleMobileSubmenu = (menu) => {
  mobileOpenMenu.value =
    mobileOpenMenu.value === menu ? null : menu;
};

/*
|--------------------------------------------------------------------------
| Close Everything
|--------------------------------------------------------------------------
*/

const closeAllMenus = () => {
  menuOpen.value = false;
  mobileOpenMenu.value = null;
  servicesMenuOpen.value = false;
  industriesMenuOpen.value = false;
};
</script>

<style scoped>

/*
|--------------------------------------------------------------------------
| Mobile Menu Animation
|--------------------------------------------------------------------------
*/

.content-fade-enter-active,
.content-fade-leave-active {
  transition:
    opacity 0.2s ease,
    transform 0.2s ease;
}

.content-fade-enter-from,
.content-fade-leave-to {
  opacity: 0;
  transform: translateY(-8px);
}

/*
|--------------------------------------------------------------------------
| Mobile Submenu Animation
|--------------------------------------------------------------------------
*/

.submenu-enter-active,
.submenu-leave-active {
  transition:
    max-height 0.25s ease,
    opacity 0.2s ease;
}

.submenu-enter-from,
.submenu-leave-to {
  max-height: 0;
  opacity: 0;
}

.submenu-enter-to,
.submenu-leave-from {
  max-height: 1000px;
  opacity: 1;
}


/* Mega menu transition */
.mega-menu-enter-active,
.mega-menu-leave-active {
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.mega-menu-enter-from,
.mega-menu-leave-to {
  opacity: 0;
  transform: translateY(-12px);
}

.mega-menu-enter-to,
.mega-menu-leave-from {
  opacity: 1;
  transform: translateY(0);
}
</style>

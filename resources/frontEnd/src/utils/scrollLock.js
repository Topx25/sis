import { watch, onBeforeUnmount, toValue } from 'vue';

let originalOverflow = '';
let originalPaddingRight = '';
const activeLocks = new Set();

/**
 * Update the document body and html styles based on active scroll locks.
 * Reference-counted via activeLocks Set to prevent premature unlocking
 * when nested or multiple modals are open.
 */
function updateBodyState() {
  if (typeof document === 'undefined') return;

  const body = document.body;
  const html = document.documentElement;
  if (!body) return;

  if (activeLocks.size > 0) {
    if (!body.classList.contains('modal-open')) {
      originalOverflow = body.style.overflow;
      originalPaddingRight = body.style.paddingRight;

      // Compensate for disappearing vertical scrollbar to prevent horizontal layout shift
      const scrollbarWidth = window.innerWidth - html.clientWidth;
      if (scrollbarWidth > 0) {
        body.style.paddingRight = `${scrollbarWidth}px`;
      }

      body.style.overflow = 'hidden';
      body.classList.add('modal-open');
      html.classList.add('modal-open');
    }
  } else {
    if (body.classList.contains('modal-open')) {
      body.style.overflow = originalOverflow || '';
      body.style.paddingRight = originalPaddingRight || '';
      body.classList.remove('modal-open');
      html.classList.remove('modal-open');
      originalOverflow = '';
      originalPaddingRight = '';
    }
  }
}

/**
 * Lock background scrolling for a given key or element.
 * @param {any} key - Unique identifier or element reference
 */
export function lockBodyScroll(key = 'default') {
  activeLocks.add(key);
  updateBodyState();
}

/**
 * Unlock background scrolling for a given key or element.
 * @param {any} key - Unique identifier or element reference
 */
export function unlockBodyScroll(key = 'default') {
  activeLocks.delete(key);
  updateBodyState();
}

/**
 * Force clear all scroll locks (e.g., on route navigation).
 */
export function forceUnlockAll() {
  activeLocks.clear();
  updateBodyState();
}

/**
 * Vue Composable to automatically lock body scroll while a modal is open.
 * @param {import('vue').Ref<boolean> | (() => boolean)} isOpenRef - Reactive boolean ref or getter
 * @param {string|symbol} [customKey] - Optional identifier
 */
export function useModalScrollLock(isOpenRef, customKey) {
  const key = customKey || Symbol('modal-scroll-lock');

  watch(
    () => Boolean(toValue(isOpenRef)),
    (isOpen) => {
      if (isOpen) {
        lockBodyScroll(key);
      } else {
        unlockBodyScroll(key);
      }
    },
    { immediate: true }
  );

  onBeforeUnmount(() => {
    unlockBodyScroll(key);
  });
}

/**
 * Automatic DOM observer that detects any modal overlays added or removed
 * in the document and manages background scrolling automatically.
 */
export function initAutoModalScrollLock() {
  if (typeof window === 'undefined' || typeof MutationObserver === 'undefined') return;

  const isModalOverlay = (node) => {
    if (!node || node.nodeType !== 1) return false;
    const el = node;

    // Must be an element with fixed full-screen overlay or dialog
    const isFixedOverlay =
      el.classList?.contains('fixed') &&
      el.classList?.contains('inset-0') &&
      !el.classList?.contains('pointer-events-none') &&
      !el.hasAttribute?.('data-no-scroll-lock');

    const isDialog =
      (el.getAttribute?.('role') === 'dialog' || el.tagName === 'DIALOG') &&
      !el.classList?.contains('pointer-events-none') &&
      !el.hasAttribute?.('data-no-scroll-lock');

    return isFixedOverlay || isDialog;
  };

  const observer = new MutationObserver((mutations) => {
    for (const mutation of mutations) {
      for (const node of mutation.addedNodes) {
        if (isModalOverlay(node)) {
          lockBodyScroll(node);
        } else if (node.nodeType === 1 && node.querySelector) {
          const innerModal = node.querySelector('.fixed.inset-0:not(.pointer-events-none), [role="dialog"], dialog[open]');
          if (innerModal && !innerModal.hasAttribute('data-no-scroll-lock')) {
            lockBodyScroll(innerModal);
          }
        }
      }

      for (const node of mutation.removedNodes) {
        if (isModalOverlay(node)) {
          unlockBodyScroll(node);
        } else if (node.nodeType === 1 && node.querySelector) {
          const innerModal = node.querySelector('.fixed.inset-0:not(.pointer-events-none), [role="dialog"], dialog');
          if (innerModal) {
            unlockBodyScroll(innerModal);
          }
        }
      }
    }
  });

  if (document.body) {
    observer.observe(document.body, { childList: true, subtree: true });
  } else {
    document.addEventListener('DOMContentLoaded', () => {
      observer.observe(document.body, { childList: true, subtree: true });
    });
  }
}

export default {
  mounted(el) {
    const elements = el.querySelectorAll("[data-reveal]");

    // لو مفيش عناصر جوه الـ section
    if (!elements.length) {
      el.classList.add("reveal-section");
    }

    const observer = new IntersectionObserver(
      ([entry]) => {
        if (!entry.isIntersecting) return;

        el.classList.add("reveal-visible");

        elements.forEach((element, index) => {
          element.style.setProperty(
            "--reveal-delay",
            `${index * 120}ms`
          );

          element.classList.add("reveal-item-visible");
        });

        observer.unobserve(el);
      },
      {
        threshold: 0.15,
      }
    );

    observer.observe(el);

    el._revealObserver = observer;
  },

  unmounted(el) {
    el._revealObserver?.disconnect();
  },
};
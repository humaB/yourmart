export default {
  inserted(el, binding) {
    const loadImage = () => {
      el.src = binding.value;
    };

    const observer = new IntersectionObserver((entries, obs) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          loadImage();
          obs.unobserve(el);
        }
      });
    });

    observer.observe(el);
  }
}

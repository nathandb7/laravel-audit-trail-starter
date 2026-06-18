(function () {
  const form = document.querySelector('[data-audit-filters]');

  if (!form) {
    return;
  }

  form.addEventListener('reset', function () {
    window.setTimeout(function () {
      form.submit();
    }, 0);
  });
})();

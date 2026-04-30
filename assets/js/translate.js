(function () {
  var watching = false;

  function nukeGoogleBar() {
    // Remove top offset Google forces on body via inline style
    document.body.style.removeProperty('top');
    // Physically remove the banner element from the DOM
    var selectors = ['.goog-te-banner-frame', '#goog-gt-tt', '.goog-te-ftab-frame'];
    selectors.forEach(function (sel) {
      var el = document.querySelector(sel);
      if (el && el.parentNode) el.parentNode.removeChild(el);
    });
  }

  function startWatching() {
    if (watching) return;
    watching = true;
    var obs = new MutationObserver(nukeGoogleBar);
    // Watch body for new children being added (the banner iframe) AND style changes
    obs.observe(document.body, {
      childList: true,
      attributes: true,
      attributeFilter: ['style']
    });
  }

  function isTranslated() {
    return document.cookie.split(';').some(function (c) {
      return c.trim().indexOf('googtrans=') === 0 && c.indexOf('/en') !== -1;
    });
  }

  function updateLabel() {
    var label = document.getElementById('translate-label');
    if (label) label.textContent = isTranslated() ? 'PT' : 'EN';
  }

  window.googleTranslateElementInit = function () {
    new google.translate.TranslateElement({
      pageLanguage: 'pt',
      includedLanguages: 'en',
      autoDisplay: false
    }, 'google_translate_element');
    startWatching();
    updateLabel();
  };

  window.g6Translate = function () {
    var select = document.querySelector('.goog-te-combo');
    if (!select) return;
    select.value = isTranslated() ? '' : 'en';
    select.dispatchEvent(new Event('change'));
    // Fallback nuke after Google finishes injecting
    setTimeout(nukeGoogleBar, 50);
    setTimeout(nukeGoogleBar, 400);
    // Update label after cookie is set
    setTimeout(updateLabel, 300);
    setTimeout(updateLabel, 800);
  };

  updateLabel();
  startWatching();
})();

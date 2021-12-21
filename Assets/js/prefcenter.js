window.onload = function () {
  document.querySelector('.pref-saveprefs .btn-save').onclick = function () {
    var forms = document.getElementsByName('contact_segments');
    for (var i = 0; i < forms.length; i++) {
      if (forms[i].tagName === 'FORM') {
        forms[i].submit();
      }
    }
  }
}


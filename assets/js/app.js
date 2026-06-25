(function () {
  const cameraBtn = document.getElementById('cameraBtn');
  const photoInput = document.getElementById('photoInput');
  const uploadStatus = document.getElementById('uploadStatus');

  if (!cameraBtn || !photoInput || !uploadStatus) {
    return;
  }

  function renderStatus(message, type) {
    uploadStatus.innerHTML = '<div class="status-box ' + type + '">' + message + '</div>';
  }

  cameraBtn.addEventListener('click', function () {
    photoInput.click();
  });

  photoInput.addEventListener('change', async function () {
    const file = photoInput.files && photoInput.files[0];
    if (!file) {
      return;
    }

    const formData = new FormData();
    formData.append('photo', file);

    renderStatus(
      '<div class="d-flex align-items-center gap-2"><div class="spinner-border spinner-border-sm text-light" role="status" aria-hidden="true"></div><span>Enviando foto...</span></div>',
      ''
    );

    try {
      const response = await fetch('upload.php', {
        method: 'POST',
        body: formData
      });

      const result = await response.json();
      if (!response.ok || !result.success) {
        throw new Error(result.message || 'Não foi possível enviar a foto.');
      }

      renderStatus(result.message + ' <a href="gallery.php" class="link-light ms-2">Ver galeria</a>', 'ok');
    } catch (error) {
      renderStatus((error && error.message) || 'Erro no envio da foto.', 'error');
    } finally {
      photoInput.value = '';
    }
  });
})();

(function () {
  const cameraBtn = document.getElementById('cameraBtn');
  const photoInput = document.getElementById('photoInput');
  const uploadStatus = document.getElementById('uploadStatus');

  if (!cameraBtn || !photoInput || !uploadStatus) {
    return;
  }

  function clearStatus() {
    uploadStatus.textContent = '';
  }

  function renderStatus(message, type, linkHref) {
    clearStatus();
    const box = document.createElement('div');
    box.className = 'status-box ' + (type || '');
    box.append(document.createTextNode(message));

    if (linkHref) {
      const link = document.createElement('a');
      link.className = 'link-light ms-2';
      link.href = linkHref;
      link.textContent = 'Ver galeria';
      box.appendChild(link);
    }

    uploadStatus.appendChild(box);
  }

  function renderUploading() {
    clearStatus();
    const box = document.createElement('div');
    box.className = 'status-box';

    const wrap = document.createElement('div');
    wrap.className = 'd-flex align-items-center gap-2';

    const spinner = document.createElement('div');
    spinner.className = 'spinner-border spinner-border-sm text-light';
    spinner.setAttribute('role', 'status');
    spinner.setAttribute('aria-hidden', 'true');

    const text = document.createElement('span');
    text.textContent = 'Enviando foto...';

    wrap.appendChild(spinner);
    wrap.appendChild(text);
    box.appendChild(wrap);
    uploadStatus.appendChild(box);
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

    renderUploading();

    try {
      const response = await fetch('upload.php', {
        method: 'POST',
        body: formData
      });

      const contentType = response.headers.get('content-type') || '';
      if (!contentType.includes('application/json')) {
        throw new Error('Resposta inesperada do servidor.');
      }

      const result = await response.json();
      if (!response.ok || !result.success) {
        throw new Error(result.message || 'Não foi possível enviar a foto.');
      }

      renderStatus(result.message, 'ok', 'gallery.php');
    } catch (error) {
      renderStatus(error?.message || 'Erro no envio da foto.', 'error');
    } finally {
      photoInput.value = '';
    }
  });
})();

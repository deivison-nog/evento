<?php
$uploadsDir = __DIR__ . '/uploads';
$allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
$images = [];
$scanError = false;

if (is_dir($uploadsDir)) {
    $files = scandir($uploadsDir);
    if ($files === false) {
        $scanError = true;
        $files = [];
    }
    foreach ($files as $file) {
        if ($file === '.' || $file === '..') {
            continue;
        }

        $path = $uploadsDir . '/' . $file;
        if (!is_file($path)) {
            continue;
        }

        $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        if (!in_array($extension, $allowedExtensions, true)) {
            continue;
        }

        $mtime = filemtime($path);
        $images[] = [
            'name' => $file,
            'mtime' => $mtime === false ? 0 : $mtime,
        ];
    }
}

usort($images, static fn(array $a, array $b): int => $b['mtime'] <=> $a['mtime']);

$perPage = 10;
$totalImages = count($images);
$totalPages = max(1, (int) ceil($totalImages / $perPage));
$currentPage = max(1, min($totalPages, (int) filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT) ?: 1));
$offset = ($currentPage - 1) * $perPage;
$pageImages = array_slice($images, $offset, $perPage);
?>
<!doctype html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Galeria | 15 anos da Ana Beatriz</title>
  <link rel="preconnect" href="https://cdn.jsdelivr.net">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
  <main class="container py-4 py-md-5">
    <section class="hero-card">
      <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <div>
          <h1 class="h2 mb-1">Galeria Neon</h1>
          <p class="mb-0 text-light-emphasis">
            Fotos do aniversário de 15 anos da Ana Beatriz
            <?php if ($totalImages > 0): ?>
              &mdash; <span class="fw-semibold"><?php echo $totalImages; ?> <?php echo $totalImages === 1 ? 'foto registrada' : 'fotos registradas'; ?></span>
            <?php endif; ?>
          </p>
        </div>
        <a href="index.html" class="btn btn-outline-light">← Voltar</a>
      </div>

      <?php if (empty($images)): ?>
        <div class="alert alert-secondary mb-0" role="alert">
          Ainda não há fotos na galeria. Tire a primeira foto na página inicial. 💖
        </div>
      <?php else: ?>
        <div class="row g-3">
          <?php foreach ($pageImages as $image): ?>
            <?php
              $safeName = htmlspecialchars($image['name'], ENT_QUOTES, 'UTF-8');
              $url = 'uploads/' . rawurlencode($image['name']);
            ?>
            <div class="col-6 col-md-4 col-lg-3">
              <a href="<?php echo $url; ?>" target="_blank" rel="noopener noreferrer" class="gallery-tile d-block">
                <img src="<?php echo $url; ?>" alt="Foto enviada: <?php echo $safeName; ?>" loading="lazy">
              </a>
            </div>
          <?php endforeach; ?>
        </div>

        <?php if ($totalPages > 1): ?>
          <nav class="mt-4" aria-label="Paginação da galeria">
            <ul class="pagination pagination-neon justify-content-center mb-0 flex-wrap gap-1">
              <li class="page-item <?php echo $currentPage === 1 ? 'disabled' : ''; ?>">
                <a class="page-link" href="?page=<?php echo $currentPage - 1; ?>" aria-label="Anterior">&laquo;</a>
              </li>
              <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                <li class="page-item <?php echo $p === $currentPage ? 'active' : ''; ?>">
                  <a class="page-link" href="?page=<?php echo $p; ?>"><?php echo $p; ?></a>
                </li>
              <?php endfor; ?>
              <li class="page-item <?php echo $currentPage === $totalPages ? 'disabled' : ''; ?>">
                <a class="page-link" href="?page=<?php echo $currentPage + 1; ?>" aria-label="Próxima">&raquo;</a>
              </li>
            </ul>
            <p class="text-center mt-2 mb-0" style="font-size:.85rem;opacity:.7;">
              Página <?php echo $currentPage; ?> de <?php echo $totalPages; ?>
            </p>
          </nav>
        <?php endif; ?>
      <?php endif; ?>

      <?php if ($scanError): ?>
        <div class="alert alert-warning mt-3 mb-0" role="alert">
          Não foi possível carregar todas as fotos agora. Tente novamente em instantes.
        </div>
      <?php endif; ?>
    </section>
  </main>
</body>
</html>

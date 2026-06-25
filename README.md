# Evento - 15 anos da Ana Beatriz

Site mobile-first com tema neon (rosa e branco), pronto para hospedagem comum na Hostinger.

## Estrutura

- `/index.html` - página inicial com botão de câmera e acesso à galeria
- `/upload.php` - endpoint de upload (validação de imagem + nome único)
- `/gallery.php` - galeria com fotos salvas em `/uploads`
- `/assets/css/style.css` - estilo neon/glassmorphism
- `/assets/js/app.js` - fluxo de câmera e envio com feedback
- `/uploads/` - pasta de armazenamento das fotos

## Deploy rápido (Hostinger)

1. Envie os arquivos para `public_html`.
2. Garanta permissão de escrita na pasta `uploads/` (ex.: `755` ou conforme ambiente).
3. Acesse `index.html`.
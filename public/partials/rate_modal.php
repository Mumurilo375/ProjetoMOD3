<?php
// Modal reutilizável para avaliar filmes (nota 0..100 + comentário)
?>
<div id="rate-modal" class="modal" aria-hidden="true" role="dialog" aria-modal="true">
  <div class="modal-backdrop" data-close></div>
  <div class="modal-content" role="document">
    <button class="modal-close" aria-label="Fechar" data-close>&times;</button>
    <form method="post" action="/ProjetoMOD3-limpo/public/salvarAvaliacao.php" class="rate-form">
      <input type="hidden" name="filme_id" id="rate-filme-id" />

      <header class="rate-header">
        <h2 class="rate-title">Avalie <span id="rate-filme-titulo"></span></h2>
      </header>

      <section class="rate-body">
        <div class="rate-left">
          <img id="rate-filme-capa" src="" alt="Poster" />
          <div class="rate-meta">
            <div class="meta-year" id="rate-filme-ano"></div>
            <div class="meta-genre" id="rate-filme-genero"></div>
          </div>
        </div>
        <div class="rate-right">
          <div class="field">
            <label for="rate-nota">Nota (0–100)</label>
            <input id="rate-nota" name="nota" type="number" min="0" max="100" required placeholder="ex: 85" />
          </div>
          <div class="field">
            <label for="rate-comentario">Comentário</label>
            <textarea id="rate-comentario" name="comentario" rows="6" maxlength="1000" placeholder="Escreva seu comentário (opcional)"></textarea>
          </div>
        </div>
      </section>

      <footer class="rate-footer">
        <button class="btn btn-primary" type="submit">Salvar</button>
      </footer>
    </form>
  </div>
</div>

<script>
// Script do modal de avaliação (abre/fecha e popula com dados do filme)
(function(){
  const modal = document.getElementById('rate-modal');
  if(!modal) return;
  const closeEls = modal.querySelectorAll('[data-close]');
  closeEls.forEach(el=>el.addEventListener('click', ()=>modal.setAttribute('aria-hidden','true')));

  function openRateModal(data){
    modal.setAttribute('aria-hidden','false');
    document.getElementById('rate-filme-id').value = data.id;
    document.getElementById('rate-filme-titulo').textContent = data.titulo || '';
    const capa = document.getElementById('rate-filme-capa');
    capa.src = data.capa || '';
    capa.alt = 'Poster de ' + (data.titulo || '');
    document.getElementById('rate-filme-ano').textContent = data.ano || '';
    document.getElementById('rate-filme-genero').textContent = data.genero || '';
    // limpa campos anteriores
    const nota = document.getElementById('rate-nota');
    const comentario = document.getElementById('rate-comentario');
    nota.value = '';
    comentario.value = '';
  }

  // expõe globalmente para os botões "Avaliar" chamarem
  window.openRateModal = openRateModal;
})();
</script>
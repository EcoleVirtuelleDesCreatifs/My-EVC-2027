<section class="pt-4">
  <div class="container">
    <div class="cta-band reveal">
      <div class="bg"><img decoding="async" src="/assets/site/img/ceremonie.jpg" alt=""></div>
      <div class="row g-4 align-items-center">
        <div class="col-lg-7">
          <span class="kicker light">Rentrée {{ now()->year }}–{{ now()->year + 1 }} · inscriptions ouvertes</span>
          <h2 class="mb-3">Votre place vous attend.<br><span class="hl">Choisissez votre formation.</span></h2>
          <p class="mb-0">Écrivez « INSCRIPTION » sur WhatsApp ou pré-inscrivez-vous en ligne : un conseiller vous recontacte et vous oriente.</p>
        </div>
        <div class="col-lg-5 d-flex flex-wrap gap-3 justify-content-lg-end">
          <a href="/preinscription" class="btn btn-evc btn-lg d-inline-flex align-items-center gap-2">Préinscription en ligne <svg class="ico"><use href="#i-arrow"/></svg></a>
          <a href="https://wa.me/{{ \App\Models\Setting::get('whatsapp_ci') }}?text=INSCRIPTION" target="_blank" rel="noopener" class="btn btn-outline-light btn-lg d-inline-flex align-items-center gap-2"><svg class="ico"><use href="#i-wa"/></svg> WhatsApp</a>
        </div>
      </div>
    </div>
  </div>
</section>

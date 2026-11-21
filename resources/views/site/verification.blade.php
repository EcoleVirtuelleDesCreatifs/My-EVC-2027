@extends('layouts.site')
@section('title', 'Vérifier un document · EVC — École Virtuelle des Créatifs')
@section('description', "Registre officiel de l'École Virtuelle des Créatifs : vérifiez l'authenticité d'un certificat.")
@section('page', 'jury')

@section('content')
<section class="section reg" id="verifier">
  <div class="container position-relative"><div class="row g-5 align-items-start">
    <div class="col-lg-6">
      <span class="reg-badge"><svg class="ico"><use href="#i-shield"/></svg> Registre officiel · service de consultation</span>
      <h1 class="title text-white">Vérifier l'authenticité <span class="hl">d'un document EVC</span></h1>
      <p style="color:#C2CAE6;font-size:1.06rem">Saisissez la référence imprimée en bas du certificat. Le registre affiche le titulaire, la formation et la date de délivrance.</p>
    </div>
    <div class="col-lg-6">
      <div class="reg-card">
        <div class="reg-head"><img decoding="async" src="/assets/site/img/logo-white.png" alt="EVC"><span>Registre officiel<br><small>Abidjan, Côte d'Ivoire</small></span></div>
        <form class="reg-form" method="get" action="{{ route('certificat.verifier') }}">
          <label for="regRef" class="form-label">Référence du document</label>
          <div class="reg-input"><span><svg class="ico"><use href="#i-doc"/></svg></span><input id="regRef" name="reference" value="{{ request('reference') }}" placeholder="Ex. EVC-CERT-{{ now()->year }}-…" autocomplete="off" spellcheck="false" required><button class="btn btn-evc" type="submit">Vérifier</button></div>
        </form>
        @if ($code)
          @if ($certificate)
            <div class="mt-4 p-3 rounded-3" style="background:#E8F6EE;color:#14532D">
              <b class="d-flex align-items-center gap-2"><svg class="ico"><use href="#i-check"/></svg> Document authentique</b>
              <dl class="row mb-0 mt-2 small">
                <dt class="col-5">Référence</dt><dd class="col-7">{{ $certificate->code }}</dd>
                <dt class="col-5">Titulaire</dt><dd class="col-7">{{ $certificate->student->user->name }}</dd>
                <dt class="col-5">Document</dt><dd class="col-7">Certificat professionnel</dd>
                <dt class="col-5">Formation</dt><dd class="col-7">{{ $certificate->formation->name }}</dd>
                <dt class="col-5">Appréciation</dt><dd class="col-7">{{ $certificate->appreciation }}</dd>
                <dt class="col-5">Délivré le</dt><dd class="col-7">{{ $certificate->issued_on->translatedFormat('j F Y') }}</dd>
                <dt class="col-5">Statut</dt><dd class="col-7">Valide</dd>
              </dl>
            </div>
          @else
            <div class="mt-4 p-3 rounded-3" style="background:#FDECEA;color:#9B2C20"><b>Aucun document ne correspond à « {{ $code }} ».</b><br><span class="small">Vérifiez la saisie. Si le document vous semble authentique, contactez l'EVC à {{ \App\Models\Setting::get('school_email') }}.</span></div>
          @endif
        @endif
        <div class="reg-warn mt-4"><svg class="ico"><use href="#i-shield"/></svg><span>Si les informations du registre diffèrent du document présenté, son authenticité ne peut pas être confirmée.</span></div>
      </div>
    </div>
  </div></div>
</section>
@endsection

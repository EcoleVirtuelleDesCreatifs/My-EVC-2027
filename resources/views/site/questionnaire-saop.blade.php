<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="referrer" content="no-referrer">
    <meta name="robots" content="noindex,nofollow">
    <title>Questionnaire SAOP · EVC</title>
    <style>
        *{box-sizing:border-box}body{margin:0;background:#f4f3f8;color:#1e1b4b;font:16px/1.7 Arial,sans-serif}main{max-width:900px;margin:auto;padding:24px 16px 64px}header{background:#1e1b4b;color:white;padding:32px;border-radius:16px;border-top:5px solid #ee5a36}h1{font-size:28px;line-height:1.3}h2{font-size:20px;line-height:1.4}article,.card{padding:24px;background:white;border:1px solid #e4e2f3;border-radius:12px;margin-top:20px}textarea{display:block;width:100%;padding:14px;font:inherit;border:1px solid #aaa6c7;border-radius:8px;min-height:160px;resize:vertical}textarea:focus{outline:2px solid #3a3591}button{background:#c2401f;color:white;border:0;border-radius:8px;padding:15px 22px;font:700 15px Arial;cursor:pointer}button:disabled{opacity:.6;cursor:wait}.muted{color:#5e6470;font-size:14px}.bar{position:sticky;top:0;z-index:2;background:#fff;padding:14px;border-bottom:2px solid #ee5a36;display:flex;justify-content:space-between;gap:12px;flex-wrap:wrap}li{margin-bottom:12px}.notice{border-left:4px solid #ee5a36;padding:14px;background:#fff3ef}a{color:#3a3591}label{display:block;font-weight:bold;margin-bottom:12px}#timer{font-variant-numeric:tabular-nums;font-weight:bold}.error{color:#ac291b}
    </style>
</head>
<body>
<main>
    <header>
        <strong>MY EVC · ADMISSIONS</strong>
        <h1>Questionnaire d’éligibilité à la préinscription en ligne</h1>
        <p>Dispositif SAOP · Motivation, préparation technique, autonomie et engagement</p>
        <p>{{ $c->first_name }} · Dossier {{ $c->reference }}</p>
    </header>
    @if ($c->saopClosed())
        <section class="card"><h2>Questionnaire clôturé</h2><p>{{ $c->saop_submitted_at ? 'Vos réponses ont été soumises.' : 'Les 30 minutes sont écoulées. Les réponses sauvegardées sont conservées pour examen.' }} Notre équipe étudiera votre questionnaire.</p><p>Aucune réponse ne peut désormais être modifiée.</p></section>
    @elseif (! $c->saop_started_at)
        <section class="card">
            <h2>Avant de commencer</h2>
            <p>Vous disposez de <strong>30 minutes</strong> pour répondre à 10 questions ouvertes et rédiger votre engagement personnel. Préparez les caractéristiques de votre ordinateur et les documents officiels qui vous ont été communiqués.</p>
            <p>Le temps commence uniquement au clic sur le bouton ci-dessous. Il continue si vous fermez ou rechargez cette page. Une seule tentative est prévue.</p>
            <p>Vos réponses sont sauvegardées automatiquement lorsque la connexion est disponible. Vérifiez l’indicateur de sauvegarde. À l’expiration, seules les réponses déjà enregistrées sont conservées.</p>
            <p class="notice">Votre équipement, votre lieu de résidence ou un besoin d’accompagnement ne provoquent aucune exclusion automatique. L’équipe évalue votre situation selon la formation choisie.</p>
            <form method="post">@csrf<input type="hidden" name="action" value="start"><button type="submit">Démarrer le questionnaire · 30 minutes</button></form>
        </section>
    @else
        <div class="bar"><span>Temps restant : <span id="timer" role="timer">30:00</span></span><span id="save-status" role="status">Réponses enregistrées</span></div>
        <noscript><p class="notice">Activez JavaScript pour le chronomètre et la sauvegarde automatique du questionnaire.</p></noscript>
        <form id="questionnaire" method="post">
            @csrf
            <input type="hidden" name="action" value="submit">
            @foreach ($questions as $number => [$title, $question, $hint])
                <article>
                    <h2>{{ $number }}. {{ $title }}</h2>
                    <label for="answer-{{ $number }}">{{ $question }}</label>
                    <p class="muted" id="hint-{{ $number }}">{{ $hint }}</p>
                    @if ($number === 11)
                        <details><summary>Lire les déclarations d’engagement</summary><ul>@foreach (\App\Services\SaopQuestionnaire::DECLARATIONS as $declaration)<li>{{ $declaration }}</li>@endforeach</ul>
                            <p class="notice">{{ \App\Services\SaopQuestionnaire::ENGAGEMENT }}</p>
                        </details>
                        <p class="notice">Ces déclarations se rapportent uniquement aux documents officiels effectivement communiqués. Signalez dans votre réponse tout document manquant ou toute demande d’explication. Il ne s’agit pas de cases à cocher.</p>
                    @endif
                    <textarea id="answer-{{ $number }}" name="answers[{{ $number }}]" data-question="{{ $number }}" maxlength="10000" required aria-describedby="hint-{{ $number }}" placeholder="Rédigez votre réponse ici…">{{ $c->saop_answers[$number] ?? '' }}</textarea>
                </article>
            @endforeach
            <section class="card"><p>Relisez vos réponses avant de soumettre. La soumission est définitive et l’évaluation sera effectuée par l’équipe EVC.</p><button type="submit" id="submit">SOUMETTRE MA CANDIDATURE À L’EVC</button><p id="form-status" role="status"></p></section>
        </form>
        <script>
            const form = document.getElementById('questionnaire');
            const status = document.getElementById('save-status');
            const message = document.getElementById('form-status');
            const submit = document.getElementById('submit');
            let deadline = performance.now() + @json(max(0, $c->saop_started_at->copy()->addMinutes(30)->getTimestamp() - now()->getTimestamp()) * 1000);
            let dirty = false, busy = false, closing = false, debounce;
            const values = () => Object.fromEntries([...form.querySelectorAll('textarea')].map(el => [el.dataset.question, el.value]));
            function lock() {
                closing = true;
                form.querySelectorAll('textarea,button').forEach(el => el.disabled = true);
                status.textContent = 'Questionnaire clôturé';
                message.textContent = 'Le questionnaire est clôturé. Les réponses enregistrées seront examinées par notre équipe.';
            }
            async function send(action) {
                if (busy || closing) return;
                busy = true;
                dirty = false;
                status.textContent = 'Enregistrement en cours…';
                try {
                    const response = await fetch(window.location.href, {method:'POST', headers:{'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':form.querySelector('[name="_token"]').value}, body:JSON.stringify({action, answers:values()})});
                    const data = await response.json();
                    if (!response.ok) throw new Error(response.status === 422 ? 'Veuillez renseigner toutes les réponses avant de soumettre.' : 'Sauvegarde impossible. Vérifiez votre connexion ou rechargez la page.');
                    deadline = Math.min(deadline, performance.now() + data.remaining * 1000);
                    status.textContent = dirty ? 'Modifications à sauvegarder…' : 'Réponses enregistrées';
                    status.className = '';
                    if (data.closed) lock();
                    if (data.closed && action === 'submit') message.textContent = 'Vos réponses sont enregistrées. Le questionnaire est clôturé et sera examiné par notre équipe.';
                } catch (error) {
                    dirty = true;
                    status.textContent = 'Attention : réponses non sauvegardées';
                    status.className = 'error';
                    message.textContent = error.message;
                } finally {
                    busy = false;
                    if (!closing) submit.disabled = false;
                }
            }
            form.addEventListener('input', () => {
                dirty = true;
                status.textContent = 'Modifications à sauvegarder…';
                clearTimeout(debounce);
                debounce = setTimeout(() => send('save'), 700);
            });
            form.addEventListener('submit', async event => {
                event.preventDefault();
                if (busy || closing || !confirm('Soumettre définitivement vos réponses ?')) return;
                submit.disabled = true;
                await send('submit');
            });
            setInterval(() => {
                const seconds = Math.max(0, Math.ceil((deadline - performance.now()) / 1000));
                document.getElementById('timer').textContent = `${Math.floor(seconds / 60).toString().padStart(2,'0')}:${(seconds % 60).toString().padStart(2,'0')}`;
                if (!seconds) lock();
                else if (dirty && !busy) send('save');
            }, 1000);
            window.addEventListener('beforeunload', event => {if (dirty && !closing) {event.preventDefault(); event.returnValue = '';}});
        </script>
    @endif
</main>
</body>
</html>

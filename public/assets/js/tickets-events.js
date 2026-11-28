'use strict';
document.querySelectorAll('form').forEach(form => {
    const field = name => form.elements.namedItem(name);
    const name = field('name'), slug = field('slug'), start = field('starts_at');
    if (name && slug) {
        let manual = !!slug.value;
        slug.addEventListener('input', () => { manual = !!slug.value; });
        name.addEventListener('input', () => {
            if (!manual) slug.value = name.value.normalize('NFD').replace(/[\u0300-\u036f]/g,'').toLowerCase().replace(/[^a-z0-9]+/g,'-').replace(/^-|-$/g,'').slice(0,100).replace(/-$/,'');
        });
    }
    if (start) {
        const from=field('access_from'), to=field('access_to');
        const manual=new Set([from,to].filter(el=>el.value));
        [from,to].forEach(el=>el.addEventListener('input',()=>{if(el.value)manual.add(el);else manual.delete(el);}));
        start.addEventListener('change',()=>{
            if (!start.value) return;
            const stamp=Date.parse(start.value+'Z');
            if (!Number.isFinite(stamp)) return;
            [[from,-30],[to,180]].forEach(([el,minutes])=>{
                if(!manual.has(el)) el.value=new Date(stamp+minutes*60000).toISOString().slice(0,16);
            });
        });
    }
    const output=form.querySelector('output');
    if(output && field('quota')){
        const render=()=>{
            const n=k=>Math.max(0,Number(field(k).value)||0);
            output.textContent='Personas: '+n('quota')*(n('adult_qty')+n('student_qty'))+' · Ingreso potencial: '+new Intl.NumberFormat('es-SV',{style:'currency',currency:'USD'}).format(n('quota')*n('price'));
        };
        form.addEventListener('input',render);render();
    }
    if(form.dataset.confirm)form.addEventListener('submit',e=>{if(!window.confirm(form.dataset.confirm))e.preventDefault();});
});

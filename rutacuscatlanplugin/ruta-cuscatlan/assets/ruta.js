document.addEventListener('DOMContentLoaded',()=>{
 const root=document.querySelector('.ruta .explorar');if(!root)return;
 const n=s=>(s||'').toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g,'');
 const cards=[...root.querySelectorAll('.card[data-b]')],q=root.querySelector('#r-q'),dep=root.querySelector('#r-d'),tip=root.querySelector('#r-t'),hos=root.querySelector('#r-h'),clr=root.querySelector('#r-x'),chips=[...root.querySelectorAll('.chip')];
 let cat=root.dataset.cat||'';cards.forEach(c=>c._b=n(c.dataset.b));
 const sing=w=>w.length>3&&w.endsWith('s')?w.slice(0,-1):w;
 function ok(c,ws){return ws.every(w=>c._b.includes(w)||c._b.includes(sing(w)))}
 const ign=['con','de','en','y','el','la','los','las','para','que','donde','hay','un','una','al','del'],flag=['hospedaje','hospedajes','alojamiento','alojamientos','dormir'];
 /* Guarda los filtros en la dirección para poder compartir la búsqueda (solo en Explorar) */
 function url(){if(tip||!window.history.replaceState)return;try{const u=new URL(location.href);
  [['ruta_q',q&&q.value.trim()],['ruta_cat',cat],['ruta_dep',dep&&dep.value]].forEach(([k,v])=>v?u.searchParams.set(k,v):u.searchParams.delete(k));
  history.replaceState(null,'',u)}catch(e){}}
 function run(){const raw=n(q.value).split(/\s+/).filter(Boolean);const pide=raw.some(w=>flag.includes(w));const ws=raw.filter(w=>!ign.includes(w)&&!flag.includes(w));let v=0;
  cards.forEach(c=>{const show=ok(c,ws)&&(!cat||c.dataset.c===cat)&&(!dep||!dep.value||(c.dataset.d||'').includes(dep.value))&&(!tip||!tip.value||c.dataset.t===tip.value)&&(!pide||c.dataset.h==='1')&&(!hos||!hos.checked||c.dataset.h==='1');c.hidden=!show;if(show)v++});
  const cn=root.querySelector('#r-n');if(cn)cn.textContent=v+(v===1?' resultado':' resultados');const z=root.querySelector('#r-0');if(z)z.hidden=v>0;
  chips.forEach(b=>b.setAttribute('aria-pressed',String((b.dataset.cat||'')===cat)));url()}
 chips.forEach(b=>b.onclick=()=>{cat=b.dataset.cat||'';run()});
 if(clr)clr.onclick=()=>{q.value='';cat='';if(dep)dep.value='';if(tip)tip.value='';if(hos)hos.checked=false;run();q.focus()};
 [q,dep,tip,hos].forEach(e=>e&&e.addEventListener('input',run));run();
});

/* Estado «abierto ahora» en tiempo real (hora de El Salvador), se actualiza cada minuto */
document.addEventListener('DOMContentLoaded',()=>{
 const els=[...document.querySelectorAll('.rc-abierto[data-h]')];if(!els.length)return;
 function ahora(){const p=new Intl.DateTimeFormat('en-US',{timeZone:'America/El_Salvador',weekday:'short',hour:'2-digit',minute:'2-digit',hourCycle:'h23'}).formatToParts(new Date());const g=t=>p.find(x=>x.type===t).value;
  return{d:['Sun','Mon','Tue','Wed','Thu','Fri','Sat'].indexOf(g('weekday')),m:(+g('hour'))*60+(+g('minute'))}}
 function abierto(w,t){if((w[t.d]||[]).some(r=>t.m>=r[0]&&t.m<r[1]))return true;return(w[(t.d+6)%7]||[]).some(r=>r[1]>1440&&t.m<r[1]-1440)}
 function pintar(){const t=ahora();els.forEach(e=>{if(e.dataset.libre)return;let w;try{w=JSON.parse(e.dataset.h)}catch(x){return}const o=abierto(w,t);e.className='tag rc-abierto '+(o?'ok':'no');e.textContent=o?'● Abierto ahora':'● Cerrado ahora'});
  document.querySelectorAll('.ruta-horario tr[data-d]').forEach(r=>r.classList.toggle('hoy',+r.dataset.d===t.d))}
 pintar();setInterval(pintar,60000);
});

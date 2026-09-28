(() => {
    const key='prismSupportRequests';
    function read(storage) {
        const records=JSON.parse(storage.getItem(key)||'[]');
        if(!Array.isArray(records))throw new Error('Support requests could not be loaded.');
        return records.filter(record=>record&&typeof record==='object');
    }
    function filterRequests(records,{search='',role='',type=''}={}) {
        const query=search.toLowerCase();
        return records.filter(r=>(!role||r.role===role)&&(!type||r.type===type)&&[r.name,r.email,r.subject,r.message].some(value=>String(value||'').toLowerCase().includes(query))).sort((a,b)=>(Date.parse(b.date)||0)-(Date.parse(a.date)||0));
    }
    function submit(storage,request) {
        const subject=String(request.subject||'').trim(),message=String(request.message||'').trim();
        if(!subject||!message)throw new Error('Enter a subject and message.');
        if(subject.length>120||message.length>1000)throw new Error('Keep the subject under 121 characters and message under 1,001 characters.');
        const records=read(storage);records.unshift({...request,subject,message,status:'Open'});storage.setItem(key,JSON.stringify(records));
    }
    if(typeof module!=='undefined'&&module.exports){module.exports={read,filterRequests,submit};return;}
    const $=id=>document.getElementById(id),esc=value=>String(value??'').replace(/[&<>"']/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
    if($('supportRequestList')) {
        function render(){try{const records=filterRequests(read(localStorage),{search:$('supportSearch').value,role:$('supportRoleFilter').value,type:$('supportTypeFilter').value});$('supportRequestCount').textContent=`${records.length} ${records.length===1?'request':'requests'}`;$('supportRequestList').innerHTML=records.map(r=>{const timestamp=Date.parse(r.date),date=Number.isFinite(timestamp)?new Date(timestamp).toLocaleString('en-PH'):'Date not recorded';return `<details class="support-request"><summary>${esc(r.subject||'Support request')}</summary><div class="support-request-meta">${esc(r.name||'Name not recorded')} · ${esc(r.role||'Role not recorded')}<br>${esc(r.email||'Email not recorded')}<br>${esc(r.type||'Support request')} · ${esc(r.status||'Open')} · ${esc(date)}</div><div class="support-request-message">${esc(r.message)}</div></details>`;}).join('')||'<p class="support-empty">No support requests match this view.</p>';}catch(_){$('supportRequestCount').textContent='Unable to load support requests.';$('supportRequestList').replaceChildren();}}
        $('supportSearch').addEventListener('input',render);$('supportRoleFilter').addEventListener('change',render);$('supportTypeFilter').addEventListener('change',render);$('refreshSupport').addEventListener('click',render);window.addEventListener('storage',event=>{if(event.key===key||event.key===null)render();});window.addEventListener('pageshow',render);render();
    }
    if($('adviserSupportForm')) {
        const dialog=$('adviserSupportDialog');
        $('adviserSupportButton').addEventListener('click',()=>{$('adviserSupportStatus').textContent='';dialog.showModal();$('adviserSupportSubject').focus();});
        $('closeAdviserSupport').addEventListener('click',()=>dialog.close());$('cancelAdviserSupport').addEventListener('click',()=>dialog.close());
        $('adviserSupportForm').addEventListener('submit',event=>{event.preventDefault();try{let email=document.body.dataset.email||'',userKey=document.body.dataset.adviserId||'';if(!email){try{const account=JSON.parse(sessionStorage.getItem('prismCurrentAccount')||'null');if(account&&['adviser','faculty'].includes(account.role)){email=account.email;userKey=email;}}catch(_){}}submit(localStorage,{id:crypto.randomUUID(),userKey,name:document.body.dataset.name||'Research Adviser',email,role:'Research Adviser',type:$('adviserSupportType').value,subject:$('adviserSupportSubject').value,message:$('adviserSupportMessage').value,date:new Date().toISOString()});event.target.reset();$('adviserSupportStatus').textContent='Request saved. It is available in the RPMS Help & Support inbox in this browser.';}catch(error){$('adviserSupportStatus').textContent=error.message||'Unable to save your request. Please try again.';}});
    }
})();

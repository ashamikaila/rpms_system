const test=require('node:test');
const assert=require('node:assert/strict');
const vm=require('node:vm');
const fs=require('node:fs');
function setup(){
    const nodes=new Map(),data=new Map([['prismStudents:admin',JSON.stringify([{id:1},{id:2}])]]);
    function get(id){if(!nodes.has(id))nodes.set(id,{value:'',checked:false,hidden:false,textContent:'',innerHTML:'',listeners:{},addEventListener(name,fn){this.listeners[name]=fn;},showModal(){this.open=true;},close(){this.open=false;},focus(){},reset(){get('noticeAudience').value='All Students';get('noticeType').value='Status Update';get('noticeMessage').value='';get('noticeGroup').value='';get('automatedNotice').checked=false;get('noticeSchedule').value='';}});return nodes.get(id);}
    get('noticeAudience').value='All Students';get('noticeType').value='Reminder';
    vm.runInNewContext(fs.readFileSync('assets/js/admin-notifications.js','utf8'),{document:{body:{dataset:{adminUser:'admin'}},getElementById:get,addEventListener:(event,fn)=>fn()},localStorage:{getItem:key=>data.get(key)||null,setItem:(key,value)=>data.set(key,value)},Date});
    return {get,data,submit:()=>get('notificationForm').listeners.submit({preventDefault(){}})};
}
test('confirmation and cancellation do not save a notification',()=>{
    const {get,data,submit}=setup();get('noticeMessage').value='Reminder';submit();assert.equal(get('notificationConfirm').open,true);assert.match(get('notificationConfirmMessage').textContent,/2 student/);assert.equal(data.has('prismAdminNotifications:admin'),false);get('cancelConfirm').listeners.click();assert.equal(data.has('prismAdminNotifications:admin'),false);assert.equal(get('noticeMessage').value,'Reminder');
});
test('confirm preserves existing notification fields and reminder schedule',()=>{
    const {get,data,submit}=setup();get('noticeMessage').value=' Please upload your requirement. ';get('automatedNotice').checked=true;get('noticeSchedule').value='2026-10-10T09:30';submit();get('confirmSend').listeners.click();const saved=JSON.parse(data.get('prismAdminNotifications:admin'));assert.equal(saved.length,1);assert.equal(saved[0].message,'Please upload your requirement.');assert.equal(saved[0].audience,'All Students');assert.equal(saved[0].automated,true);assert.equal(saved[0].date,new Date('2026-10-10T09:30').toISOString());assert.equal(saved[0].subject,undefined);assert.match(get('noticeHistory').innerHTML,/Reminder saved locally/);
});

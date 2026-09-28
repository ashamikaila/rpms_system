const test=require('node:test');
const assert=require('node:assert/strict');
const {read,filterRequests,submit}=require('../assets/js/support.js');
function storage(initial='[]'){let value=initial;return {getItem:()=>value,setItem:(key,next)=>{assert.equal(key,'prismSupportRequests');value=next;}};}
test('adviser requests join existing student requests without replacing them',()=>{
    const store=storage(JSON.stringify([{id:'student',role:'Student',subject:'Upload question',message:'Help'}]));
    submit(store,{id:'adviser',role:'Research Adviser',subject:' Review concern ',message:' Please check ',date:'2026-09-28'});
    const records=read(store);assert.equal(records.length,2);assert.equal(records[0].subject,'Review concern');assert.equal(records[0].status,'Open');assert.equal(records[1].id,'student');
});
test('inbox filters role, type and request contents',()=>{
    const records=[{role:'Student',type:'Report a problem',message:'Upload failed',date:'2026-09-27'},{role:'Research Adviser',type:'Feedback or concern',subject:'Review issue',date:'2026-09-28'}];
    assert.equal(filterRequests(records,{search:'UPLOAD',role:'Student'}).length,1);
    assert.equal(filterRequests(records,{type:'Feedback or concern'})[0].role,'Research Adviser');
    assert.equal(filterRequests(records)[0].subject,'Review issue');
});
test('invalid requests and corrupt storage do not overwrite saved requests',()=>{
    const store=storage();assert.throws(()=>submit(store,{subject:' ',message:'text'}));assert.deepEqual(read(store),[]);
    const broken=storage('{');assert.throws(()=>submit(broken,{subject:'Subject',message:'Message'}));assert.equal(broken.getItem(),'{' );
});

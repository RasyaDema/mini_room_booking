// Minimal JS: modal, toast auto-hide, client-side validation and event delegation
document.addEventListener('DOMContentLoaded', function(){
    // open booking modal
    document.querySelectorAll('.open-booking').forEach(btn=>{
        btn.addEventListener('click', e=>{
            const id = btn.dataset.roomId;
            const modal = document.getElementById('bookingModal');
            document.getElementById('room_id').value = id;
            modal.setAttribute('aria-hidden','false');
        });
    });
    // close
    document.querySelectorAll('.modal .close').forEach(b=>b.addEventListener('click', ()=>{
        b.closest('.modal').setAttribute('aria-hidden','true');
    }));

    // toast auto hide
    document.querySelectorAll('.toast').forEach(t=>{
        setTimeout(()=>{ t.style.display='none'; }, 3500);
    });

    // booking form simple client-side validation
    const bookingForm = document.getElementById('bookingForm');
    if (bookingForm) bookingForm.addEventListener('submit', function(e){
        const start = document.getElementById('start_time').value;
        const end = document.getElementById('end_time').value;
        if (!start || !end || start >= end) {
            e.preventDefault();
            alert('Please provide a valid start and end time.');
        }
    });
});

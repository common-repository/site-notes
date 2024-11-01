// Front end note meta box in admin bar
document.addEventListener('DOMContentLoaded', function () {
    const toggleNoteButton = document.getElementById('toggle-note');
    const noteBox = document.querySelector('.note-box');
    const sn_textarea = document.querySelector('#note2');
    const snStatus = document.getElementById('sn_status');
    const draggableHandle = document.querySelector('.note-box .sn_drag');
    const ajaxLoc = document.getElementById('sn_ajax_loc').value;
    const ID = document.getElementById('sn_post_id').value;
    let posX = 0, posY = 0, mouseX = 0, mouseY = 0;
    let isSnBusy = false;
    let siteNoteBounding = noteBox.getBoundingClientRect(); // Get position of site note box
    let siteNoteBounding2;


    // Show/Hide the note
    toggleNoteButton.addEventListener('click', function () {
        noteBox.style.display = (noteBox.style.display === 'none' || noteBox.style.display === '') ? 'flex' : 'none';
    });


    async function sn_saveMeta(meta, value, message) {
        try {
            const response = await fetch(`${ajaxLoc}ajax-calls.php`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `meta=${meta}&id=${ID}&value=${value}`
            });
            if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);
            snStatus.textContent = '\u2026'+message;
            if(!isSnBusy) {
                isSnBusy = true;
                snStatus.style.display = 'block';
                setTimeout(() => { 
                    snStatus.style.display = 'none';
                    isSnBusy = false;
                }, 2000);
            }
            siteNoteBounding = noteBox.getBoundingClientRect(); // Save new current position of site note box
        } catch (error) {console.error('Error:', error)}
    }


    // Save note box page note with ajax
    document.getElementById('submit2').addEventListener('click', async function () {
        sn_saveMeta('note', sn_textarea.value, 'Note Saved');
    });


    // Make note lock open/close save with ajax call
    document.getElementById('lock_notes_on').addEventListener('change', async function () {
        const lock = this.checked ? 'lock' : '';
        sn_saveMeta('lock_notes_on', lock, 'Saving Lock Setting');
    });


    draggableHandle.addEventListener('mousedown', sn_mouseDown, false);
    window.addEventListener('mouseup', sn_mouseUp, false);


    function sn_mouseDown(e) {
        e.preventDefault();
        posX = e.clientX - noteBox.offsetLeft;
        posY = e.clientY - noteBox.offsetTop;
        window.addEventListener('mousemove', sn_moveElement, false);
    }


    function sn_mouseUp() {
        siteNoteBounding2 = noteBox.getBoundingClientRect(); // Get position of site note box
        window.removeEventListener('mousemove', sn_moveElement, false);
        // Code for handling position changes
        if (siteNoteBounding2.top != siteNoteBounding.top || siteNoteBounding2.left != siteNoteBounding.left) {
            const topStyle = getComputedStyle(noteBox).top;
            const leftStyle = getComputedStyle(noteBox).left;
            const style = `top:${topStyle}; left:${leftStyle};`;
            sn_saveMeta('notes-position', style, 'Position Saved');
        }

        //Code for handling resize changes
        if ((siteNoteBounding.width != 0 && siteNoteBounding.width != siteNoteBounding2.width) || (siteNoteBounding.height != 0 && siteNoteBounding.height != siteNoteBounding2.height)) {
            const heightStyle = getComputedStyle(noteBox).height;
            const widthStyle = getComputedStyle(noteBox).width;
            const style = `height:${heightStyle}; width:${widthStyle};`;
            sn_saveMeta('textarea-size', style, 'Dimensions Saved');
        }
    }


    function sn_moveElement(e) {
        mouseX = e.clientX - posX;
        mouseY = e.clientY - posY;
        noteBox.style.left = mouseX + 'px';
        noteBox.style.top = mouseY + 'px';
    }
});
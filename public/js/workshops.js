function addWorkshop() {
    const list = document.getElementById('workshops-list');
    const div = document.createElement('div');
    div.className = 'workshop-entry';

    const input = document.createElement('input');
    input.type = 'text';
    input.name = 'workshops[]';
    input.placeholder = 'Workshop name';
    input.ariaLabel = 'Workshop name';
    input.required = true;

    div.appendChild(input);
    list.appendChild(div);

    return div;
}

function removeWorkshopWDel(buttonElement) {
    buttonElement.closest('.workshop-entry').remove();
}

function addWorkshopWDel() {
    const workshop_entry = addWorkshop();

    const delBtn = document.createElement('button');
    delBtn.type = 'button';
    delBtn.textContent = 'Delete';
    delBtn.className = 'del-workshop-btn';

    delBtn.addEventListener('click', (event) => {
        removeWorkshopWDel(event.target);
    });

    workshop_entry.appendChild(delBtn);
}

const addWorkshopBtn = document.getElementById('add-workshop-btn');

if (addWorkshopBtn) {
    addWorkshopBtn.addEventListener('click', addWorkshop);
}

const addWorkshopWDelBtn = document.getElementById('add-workshop-w-del-btn');
if (addWorkshopWDelBtn) {
    addWorkshopWDelBtn.addEventListener('click', addWorkshopWDel);
}

const list = document.getElementById('workshops-list');

list.addEventListener('click', (event) => {
    if (event.target.classList.contains('del-workshop-btn')) {
        removeWorkshopWDel(event.target);
    }
});
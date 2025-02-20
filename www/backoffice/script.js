let users = [];
<<<<<<< Updated upstream

document.addEventListener("DOMContentLoaded", function() {
    // Initialisation DataTable
    let table = $('#userTable').DataTable();
    
    // Gestion utilisateur
    document.getElementById("addUserForm").addEventListener("submit", function(e) {
        e.preventDefault();
        
        const name = document.getElementById("name").value;
        const email = document.getElementById("email").value;
        const role = document.getElementById("role").value;
        
        const newUser = { name, email, role };
        users.push(newUser);
        
        // Ajouter ligne DataTable
        let rowNode = table.row.add([
            newUser.name,
            newUser.email,
            newUser.role,
            `<button class="btn btn-info" onclick="showDetails(${users.length - 1})" data-bs-toggle="modal" data-bs-target="#userDetailModal">Voir</button>`
        ]).draw().node();
        
        // Réinitialisation formulaire
        document.getElementById("addUserForm").reset();
        let addUserModal = bootstrap.Modal.getInstance(document.getElementById("addUserModal"));
        addUserModal.hide();
=======
function renderTable() {
    const tbody = document.getElementById("userTableBody");
    tbody.innerHTML = "";
    users.forEach((user, index) => {
        tbody.innerHTML += `
    <tr>
        <td>${user.name}</td>
        <td>${user.email}</td>
        <td>${user.role}</td>
        <td><button class="btn btn-info" onclick="showDetails(${index})"
        data-bs-toggle="modal" data-bs-target="#userDetailModal">Voir</button></td>
    </tr>
    `;
>>>>>>> Stashed changes
    });
});

function showDetails(index) {
    const user = users[index];
    document.getElementById("detailName").innerText = user.name;
    document.getElementById("detailEmail").innerText = user.email;
    document.getElementById("detailRole").innerText = user.role;
<<<<<<< Updated upstream
}
=======
}
document.getElementById("addUserForm").addEventListener("submit", function (e) {
    e.preventDefault();
    const name = document.getElementById("name").value;
    const email = document.getElementById("email").value;
    const role = document.getElementById("role").value;
    users.push({ name, email, role });
    renderTable();
});


document.addEventListener("DOMContentLoaded", function() {
    // Initialisation DataTable
    let table = $('#userTable').DataTable();
    
    // Gestion utilisateur
    document.getElementById("addUserForm").addEventListener("submit", function(e) {
        e.preventDefault();
        
        const name = document.getElementById("name").value;
        const email = document.getElementById("email").value;
        const role = document.getElementById("role").value;
        
        const newUser = { name, email, role };
        users.push(newUser);
        
        // Ajouter ligne DataTable
        let rowNode = table.row.add([
            newUser.name,
            newUser.email,
            newUser.role,
            `<button class="btn btn-info" onclick="showDetails(${users.length - 1})" data-bs-toggle="modal" data-bs-target="#userDetailModal">Voir</button>`
        ]).draw().node();
        
        // Réinitialisation formulaire 
        document.getElementById("addUserForm").reset();
        let addUserModal = bootstrap.Modal.getInstance(document.getElementById("addUserModal"));
        addUserModal.hide();
    });
});

function showDetails(index) {
    const user = users[index];
    document.getElementById("detailName").innerText = user.name;
    document.getElementById("detailEmail").innerText = user.email;
    document.getElementById("detailRole").innerText = user.role;
}
>>>>>>> Stashed changes

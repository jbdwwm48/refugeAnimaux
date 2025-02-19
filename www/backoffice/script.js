js
let users = [];
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
    });
}
function showDetails(index) {
    const user = users[index];
    document.getElementById("detailName").innerText = user.name;
    document.getElementById("detailEmail").innerText = user.email;
    document.getElementById("detailRole").innerText = user.role;
}
document.getElementById("addUserForm").addEventListener("submit", function (e) {
    e.preventDefault();
    const name = document.getElementById("name").value;
    const email = document.getElementById("email").value;
    const role = document.getElementById("role").value;
    users.push({ name, email, role });
    renderTable();
});
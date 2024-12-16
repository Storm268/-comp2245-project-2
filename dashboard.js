document.addEventListener("DOMContentLoaded", function () {
    loadIssues('all'); // Load all issues by default
});

function loadIssues(filter) {
    fetch(`get-issues.php?filter=${filter}`, { credentials: 'same-origin' })
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById("issuesContainer");
            container.innerHTML = "";
            if (data.success) {
                data.issues.forEach(issue => {
                    const issueElement = document.createElement("div");
                    issueElement.className = "issue";
                    issueElement.innerHTML = `
                        <h3>${issue.title}</h3>
                        <p>Type: ${issue.type}</p>
                        <p>Priority: ${issue.priority}</p>
                        <p>Status: ${issue.status}</p>
                        <p>Assigned To: ${issue.assigned_to}</p>
                        <p>Created At: ${new Date(issue.created_at).toLocaleString()}</p>
                    `;
                    container.appendChild(issueElement);
                });
            } else {
                container.innerHTML = `<p>${data.error}</p>`;
            }
        })
        .catch(error => console.error("Error fetching issues:", error));
}

document.addEventListener("DOMContentLoaded", () => {
    const params = new URLSearchParams(window.location.search);
    const issueId = params.get("id");
    if (!issueId) {
        alert("Issue ID is missing.");
        return;
    }

    fetch(`get_issue.php?id=${issueId}`)
        .then((response) => response.json())
        .then((data) => {
            if (data.error) {
                alert(data.error);
            } else {
                document.getElementById("issue-title").textContent = data.title;
                document.getElementById("issue-description").textContent = data.description;
                document.getElementById("issue-number").textContent = `Issue #${data.id}`;
                document.getElementById("assigned-to").textContent = data.assigned_to_name;
                document.getElementById("issue-type").textContent = data.type;
                document.getElementById("issue-priority").textContent = data.priority;
                document.getElementById("issue-status").textContent = data.status;
                document.getElementById("created-on").textContent = data.created_at;
                document.getElementById("created-by").textContent = data.created_by_name;
                document.getElementById("last-updated").textContent = data.updated_at;

                document.getElementById("form-issue-id").value = data.id;
            }
        })
        .catch((error) => {
            console.error("Error fetching issue details:", error);
        });
});

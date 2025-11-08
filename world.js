// world.js
document.addEventListener("DOMContentLoaded", () => {
    const input = document.getElementById("country");
    const btn   = document.getElementById("lookup");
    const box   = document.getElementById("result");
  
    async function runLookup() {
      const q = (input.value || "").trim();
      const url = q === "" ? "world.php" : `world.php?country=${encodeURIComponent(q)}`;
  
      try {
        const resp = await fetch(url, { cache: "no-store" });
        if (!resp.ok) throw new Error(`HTTP ${resp.status}`);
        const html = await resp.text();
        box.innerHTML = html; // world.php returns HTML <ul>…</ul>
      } catch (err) {
        console.error(err);
        box.innerHTML = "<p>Could not fetch results.</p>";
      }
    }
  
    btn.addEventListener("click", runLookup);
  
    // Optional: allow Enter key in the input to trigger lookup
    input.addEventListener("keydown", (e) => {
      if (e.key === "Enter") {
        e.preventDefault();
        runLookup();
      }
    });
  });
  
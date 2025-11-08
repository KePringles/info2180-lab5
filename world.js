document.addEventListener("DOMContentLoaded", () => {
    const input = document.getElementById("country");
    const btnCountry = document.getElementById("lookup");
    const btnCities = document.getElementById("lookup-cities");
    const box = document.getElementById("result");

    async function runLookup(type = "country") {
        const q = (input.value || "").trim();
        
        let url = "";
        if (type === "cities") {
            // include lookup=cities
            url = `world.php?country=${encodeURIComponent(q)}&lookup=cities`;
        } else {
            // regular country lookup
            url = q === "" 
                ? "world.php" 
                : `world.php?country=${encodeURIComponent(q)}`;
        }

        try {
            const resp = await fetch(url, { cache: "no-store" });
            if (!resp.ok) throw new Error(`HTTP ${resp.status}`);
            const html = await resp.text();
            box.innerHTML = html;
        } catch (err) {
            console.error(err);
            box.innerHTML = "<p>Could not fetch results.</p>";
        }
    }

    btnCountry.addEventListener("click", () => runLookup("country"));
    btnCities.addEventListener("click", () => runLookup("cities"));
});

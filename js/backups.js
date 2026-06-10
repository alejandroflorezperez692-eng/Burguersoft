const STORAGE_KEY = "miBackup";

function guardarBackup() {
    const data = document.getElementById("dataInput").value;

    if (data.trim() === "") {
        alert("No hay datos para guardar.");
        return;
    }

    localStorage.setItem(STORAGE_KEY, data);
    alert("Backup guardado correctamente.");
}


function restaurarBackup() {
    const data = localStorage.getItem(STORAGE_KEY);

    if (!data) {
        alert("No existe ningún backup guardado.");
        return;
    }

    document.getElementById("dataInput").value = data;
    alert("Backup restaurado.");
}


function exportarBackup() {
    const data = localStorage.getItem(STORAGE_KEY);

    if (!data) {
        alert("No hay backup para exportar.");
        return;
    }

    const blob = new Blob([JSON.stringify({ backup: data })], { type: "application/json" });
    const url = URL.createObjectURL(blob);

    const a = document.createElement("a");
    a.href = url;
    a.download = "backup.json";
    a.click();

    URL.revokeObjectURL(url);
}


function restaurarDesdeArchivo(event) {
    const file = event.target.files[0];
    if (!file) return;

    const reader = new FileReader();

    reader.onload = function(e) {
        const contenido = JSON.parse(e.target.result);
        localStorage.setItem(STORAGE_KEY, contenido.backup);
        document.getElementById("dataInput").value = contenido.backup;
        alert("Backup restaurado desde archivo.");
    };

    reader.readAsText(file);
}


function eliminarBackup() {
    localStorage.removeItem(STORAGE_KEY);
    document.getElementById("dataInput").value = "";
    alert("Backup eliminado.");
}


document.getElementById("fileInput").addEventListener("change", restaurarDesdeArchivo);


document.querySelector(".buttons").innerHTML += 
    '<button onclick="document.getElementById(\'fileInput\').click()">Importar Backup</button>';
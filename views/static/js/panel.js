document.addEventListener('DOMContentLoaded', async () => {
    const data = await fetch('https://mattprofe.com.ar/proyectos/app-estacion/datos.php?mode=list-stations');
    const dataApi = await data.json();

    console.log(dataApi);
    
    dataApi.forEach(dato => {
        const tr = document.createElement('tr');
        tr.className = "datos"
        tr.addEventListener("click", () => {
            window.location = `detalle?estacion=${dato.chipid}`
        })

        const td1 = elementCreate('td', dato.apodo);
        const td2 = elementCreate('td', dato.ubicacion);
        const td3 = elementCreate('td', dato.visitas);

        tr.appendChild(td1);
        tr.appendChild(td2);
        tr.appendChild(td3);

        document.querySelector('tbody').appendChild(tr);
    });
});

const elementCreate = (element, content) => {
    const elemento = document.createElement(element);
    elemento.innerText = content;
    return elemento;
};

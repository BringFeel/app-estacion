const timeRefresh = 6000
let dataApi
const params = new URLSearchParams(window.location.search);

const init = async () => {

    const data = await fetch(`https://mattprofe.com.ar/proyectos/app-estacion/datos.php?chipid=${params.get('estacion')}&cant=7`);
    dataApi = await data.json();

    const dataActual = dataApi[0];

    titulo.innerText = dataActual.estacion;
    
    chargeDatosActuales(
        dataActual.temperatura,
        fireDanger(dataActual.fwi),
        dataActual.humedad,
        dataActual.presion,
        dataActual.viento,
        dataActual.veleta,
        dataActual.fecha,
        dataActual.ubicacion
    );

    const template = document.getElementById(`temperaturaTemplate`).content.cloneNode(true);
    templateContent.appendChild(template);
    cargarTemp(dataActual);
};

const refreshFetch = async () => { 

    const data = await fetch(`https://mattprofe.com.ar/proyectos/app-estacion/datos.php?chipid=${params.get('estacion')}&cant=1`);
    dataApi = await data.json();

    setInterval( async () => {
        
        const data = await fetch(`https://mattprofe.com.ar/proyectos/app-estacion/datos.php?chipid=${params.get('estacion')}&cant=1`);
        dataApi = await data.json();

        chargeDatosActuales(
            dataApi[0].temperatura,
            fireDanger(dataApi[0].fwi),
            dataApi[0].humedad,
            dataApi[0].presion,
            dataApi[0].viento,
            dataApi[0].veleta,
            dataApi[0].fecha,
            dataApi[0].ubicacion
        );
        
        refreshFetch();
        
    }, timeRefresh);
    
};

function fireDanger(fwi){
    let fwiFloat = parseFloat(fwi);
	
	if(fwiFloat>=50){
        return "Extremo";
	}else if(fwiFloat>=38){
        return "Muy alto";
	}else if(fwiFloat>=21.3){
        return "Alto";
	}else if(fwiFloat>=11.2){
        return "Moderado";
	}else if(fwiFloat>=5.2){
        return "Bajo";
	}else{
        return "Muy bajo";
	}
}

init();
refreshFetch();

const chargeDatosActuales = (temp,fire,water,pre,wind,ubiViento,date,ubi) => {
    tempActual.innerText = `${temp}°C`;
    fireActual.innerText = `${fire}`;
    waterActual.innerText = `${water}%`;
    presionActual.innerText = `${pre}hPa`;
    windActual.innerText = `${wind}Km/H`;
    ubiActual.innerText = `${ubiViento}`;

    const [dia, hora] = date.split(' ');

    fecha.innerText = `${formatDate(dia)} ${hora}`;
    ubicacion.innerText = `${ubi}`;
};

control.querySelectorAll('.btn').forEach(btn => {
    btn.addEventListener('click',() => {

        control.querySelectorAll('.btn').forEach(remove => {
            remove.style.backgroundColor = "";
            if (remove.querySelector('.select')) {
                remove.querySelector('.select').remove();
            }
        });

        btn.style.backgroundColor = "#7b7b7b";

        const select = document.createElement('div');
        select.className = "select";
        btn.appendChild(select);

        console.log(dataApi);
        
        const key = btn.attributes.key.value;

        const template = document.getElementById(`${key}Template`).content.cloneNode(true);

        templateContent.innerHTML = "";
        templateContent.appendChild(template);

        switch (key) {
            case 'temperatura':
                cargarTemp(dataApi[0]);
                break;
            case 'incendio':
                cargarIncendio(dataApi[0]);
                break;
            case 'humedad':
                cargarHumedad(dataApi[0]);
                break;
            case 'presion':
                cargarPresion(dataApi[0]);
                break;
            case 'viento':
                cargarViento(dataApi[0]);
                break;
        }
        
    });
});

const cargarTemp = (data) => {
    const temp = data.temperatura;
    const tempMax = data.tempmax;
    const tempMin = data.tempmin;
    const sens = data.sensacion;
    const sensMax = data.sensamax;
    const sensMin = data.sensamin;
    
    const [ tempUnit, tempUnitDecimal ] = temp.split('.');
    const [ sensUnit, sensUnitDecimal ] = sens.split('.');
    
    unitTemp.innerText = tempUnit;
    unitDecimalTemp.innerText = `,${tempUnitDecimal}`;
    maxTemp.innerText = `${tempMax}°C`;
    minTemp.innerText = `${tempMin}°C`;
    
    unitSens.innerText = sensUnit;
    unitDecimalSens.innerText = `,${sensUnitDecimal}`;
    maxSens.innerText = `${sensMax}°C`;
    minSens.innerText = `${sensMin}°C`;
};

const cargarIncendio = (data) => {
    const ffmc = data.ffmc;
    const dmc = data.dmc;
    const dc = data.dc;
    const isi = data.isi;
    const bui = data.bui;
    const fwi = data.fwi;

    const values = incendioCard.querySelectorAll('h4');

    values[0].innerText = ffmc;
    values[1].innerText = dmc;
    values[2].innerText = dc;
    values[3].innerText = isi;
    values[4].innerText = bui;
    values[5].innerText = fwi;
};

const cargarHumedad = (data) => {
    const [humedad, humedadDecimal] = data.humedad.split('.');
    unitHumedad.innerText = humedad;
    unitHumedadDecimal.innerText = humedadDecimal;
};

const cargarPresion = (data) => {
    const [presion, presionDecimal] = data.presion.split('.');
    unitPresion.innerText = presion;
    unitPresionDecimal.innerText = presionDecimal;
};

const cargarViento = (data) => {
    const [viento, vientoDecimal] = data.viento.split('.');
    const vientoMax = data.maxviento;
    const direccion = data.veleta;

    unitViento.innerText = viento;
    unitVientoDecimal.innerText = vientoDecimal;
    vientoMaximo.innerText = `${vientoMax}Km/H`;
    direccionViento.innerText = direccion;
};

const formatDate = (dateStr) => {
    if (dateStr !== "0000-00-00" && dateStr != "--") {
        const dateObj = new Date(dateStr);
        dateObj.setDate(dateObj.getDate() + 1);

        const meses = ["Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic"];
        return `${String(dateObj.getDate()).padStart(2, '0')}/${meses[dateObj.getMonth()]}/${dateObj.getFullYear()}`;
    } else {
        return "Sin registro";
    }
};

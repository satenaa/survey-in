$(document).ready(async function () {
    let getData = async (path, id) => {
        // let url = "http://survey.idekite.id/api";
        let url = "http://127.0.0.1:8000/api";

        let fd = new FormData();
        fd.append("id", id);
        let requestOptions = {
            method: "POST",
            Headers: {
                "Content-Type": "application/json",
            },
            body: fd,
        };
        return fetch(`${url}${path}`, requestOptions)
            .then((response) => response.text())
            .then((result) => {
                return JSON.parse(result);
            })
            .catch((error) => console.log("error", error));
    };
    let setKecamatan = async (idKab = 13) => {
        let data;
        try {
            data = await getData(`/kecamatan`, idKab);
            setResumeSurvey(data.data[0].id);
        } catch (error) {}
        $("#kecamatan").html("");
        $("#kecamatan").append(
            `<option value="" selected>Pilih Kecamatan</option> `
        );
        data.data.forEach((element) => {
            let list = document.createElement("option");
            list.innerText = `${element.nama}`;
            list.setAttribute("value", element.id);
            $("#kecamatan").append(list);
        });
    };
    let setResumeSurvey = async (idKec) => {
        let dataS = await getData("/data-survey", idKec);
        if (dataS.length == 0) {
            $("#jmlGang").text("-");
            $("#jmlRumah").text("-");
            $("#pnjJalan").text("-");
            $("#lbrJalan").text("-");
            $("#jlnJelek").text("-");
            $("#jlnBaik").text("-");
        } else {
            $("#jmlGang").text(Intl.NumberFormat("id-ID").format(dataS.jumlah));
            $("#jmlRumah").text(
                Intl.NumberFormat("id-ID").format(dataS.jumlahRumah)
            );
            $("#pnjJalan").text(
                Intl.NumberFormat("id-ID").format(dataS.panjangJalan) + " m"
            );
            $("#lbrJalan").text(
                Intl.NumberFormat("id-ID").format(dataS.lebarJalan) + " m"
            );
            $("#jlnJelek").text(
                Intl.NumberFormat("id-ID").format(dataS.jalanJelek) + "%"
            );
            $("#jlnBaik").text(
                Intl.NumberFormat("id-ID").format(dataS.jalanBaik) + "%"
            );
        }
    };
    $("#kecamatan").change(function (e) {
        $(".text-kec").text($(this).find("option:selected").text());
        e.preventDefault();
        setResumeSurvey($(this).val());
    });
    $("#kabupaten").change(function (e) {
        e.preventDefault();
        setKecamatan($(this).val());
    });
});

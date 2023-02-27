document.getElementById("modal-button").onclick = function(e) {
    e.preventDefault();
    let modal = document.getElementById("modal-result");
    modal.style.display = "none";
}

function searchBar(e) {
    e.preventDefault();
    const searchData = document.querySelector("#search").value;
    fetch("http://localhost/techtest/server.php?search="+searchData)
    .then(resp => {
        return resp.json()
    })
    .then(data => {
        searchResult(data);
    });
}

function searchResult(data) {
    const rest = document.getElementsByClassName("search__result")[0];
    rest.innerHTML = "";
    data.forEach(val => {
        let temp = `
        <a class="product__item" href="addCart.php?id=${val.id}">
            <img src="${val.image}" height="50"/>
            <div class="product__item--text">
                <h2>${val.title} - ${val.price}</h2>
                <p>Category : ${val.category} Qty : ${val.stock} Supplier : ${val.supplier}</p>
            </div>
        </a>
    `
        rest.innerHTML += temp;
    });      
}
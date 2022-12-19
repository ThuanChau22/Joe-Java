let searchTerm = "";
let timeoutId = 0;

const initialSearchTerm = () => {
  const className = "customers-search-input";
  const searchInput = document.getElementsByClassName(className)[0];
  searchTerm = searchInput.value;
}
window.addEventListener("load", initialSearchTerm);

const searchCustomers = (element) => {
  clearTimeout(timeoutId);
  timeoutId = setTimeout(() => {
    let isModified = false;
    const value = element.value.trim();
    if (searchTerm !== value) {
      searchTerm = value;
      isModified = true;
    }
    if (isModified && (searchTerm.length == 0 || searchTerm.length >= 3)) {
      const spinner = document.getElementById("search-spinner");
      spinner.innerHTML = `<i class="fa fa-spinner fa-spin"></i>`;
      const request = new XMLHttpRequest();
      request.onreadystatechange = () => {
        if (request.readyState == 4 && request.status == 200) {
          const { html } = JSON.parse(request.responseText);
          document.getElementById("customer-list").innerHTML = html;
          spinner.innerHTML = "";
        }
      };
      request.open("GET", `/src/scripts/customers.php?search=${searchTerm}`, true);
      request.send();
    }
  }, 500);
}

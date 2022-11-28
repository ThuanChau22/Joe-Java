let searchTerm = "";
let timeoutId = 0;

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
      const xhttp = new XMLHttpRequest();
      xhttp.onreadystatechange = () => {
        if (xhttp.readyState == 4 && xhttp.status == 200) {
          const customerList = document.getElementById("customer-list");
          customerList.innerHTML = xhttp.responseText;
          spinner.innerHTML = "";
        }
      };
      xhttp.open("GET", `/src/scripts/customers.php?search=${searchTerm}`, true);
      xhttp.send();
    }
  }, 500);
}

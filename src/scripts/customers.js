let searchTerm = "";
let timeoutId = 0;

const initialSearchTerm = () => {
  const className = "customers-search-input";
  const searchInput = document.getElementsByClassName(className)[0];
  searchTerm = searchInput.value;
}
window.addEventListener("load", initialSearchTerm);

const searchCustomers = (e) => {
  clearTimeout(timeoutId);
  timeoutId = setTimeout(async () => {
    try {
      let isModified = false;
      const value = e.target.value.trim();
      if (searchTerm !== value) {
        searchTerm = value;
        isModified = true;
      }
      if (isModified && (searchTerm.length == 0 || searchTerm.length >= 3)) {
        const spinner = document.getElementById("search-spinner");
        spinner.innerHTML = `<i class="fa fa-spinner fa-spin"></i>`;
        const url = `/src/api/customers.php?html&search=${searchTerm}`;
        const response = await api({ url });
        if (response) {
          document.getElementById("customer-list").innerHTML = response.html;
        }
        spinner.innerHTML = "";
      }
    } catch (error) {
      location.href = "/error";
    }
  }, 500);
}

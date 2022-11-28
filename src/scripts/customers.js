let searchTerm = "";

const searchCustomers = (element) => {
  let isModified = false;
  if (searchTerm !== element.value) {
    searchTerm = element.value;
    isModified = true;
  }
  if (isModified && (searchTerm.length == 0 || searchTerm.length >= 3)) {
    const customerList = document.getElementById("customer-list");
    const spinner = `
    <div class="customers-spinner mt-3">
      <i class="fa fa-spinner fa-spin"></i>
    </div>`;
    removeAllNodes(customerList);
    addNode(customerList, spinner)
    const xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function () {
      if (this.readyState == 4 && this.status == 200) {
        removeAllNodes(customerList);
        addNode(customerList, this.responseText);
      }
    };
    xhttp.open("GET", `/src/scripts/customers.php?search=${searchTerm}`, true);
    xhttp.send();
  }
}

const addNode = (node, content) => {
  const newNode = document.createElement("div");
  node.appendChild(newNode);
  newNode.outerHTML = content;
}

const removeAllNodes = (node) => {
  while (node.hasChildNodes()) {
    node.removeChild(node.firstChild);
  }
}

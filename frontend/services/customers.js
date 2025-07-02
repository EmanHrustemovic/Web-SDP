// Base URL of your API endpoints (adjust if needed)
const BASE_URL = "http://localhost/lastFinal/rest";

// DOM elements
const customerSelect = document.getElementById("customers-list");
const customerMealsTable = document.querySelector("#customer-meals tbody");
const addCustomerForm = document.querySelector("form");

// 1. Populate the select list with customers
async function loadCustomers() {
  try {
    const response = await fetch(`${BASE_URL}/customers`);
    const customers = await response.json();

    // Clear existing options (keep the default one)
    customerSelect.innerHTML = `<option selected>Please select one customer</option>`;

    customers.forEach((customer) => {
      const option = document.createElement("option");
      option.value = customer.id;
      option.textContent = `${customer.first_name} ${customer.last_name}`;
      customerSelect.appendChild(option);
    });
  } catch (err) {
    console.error("Failed to load customers", err);
  }
}

// 2. On select change, fetch meals for that customer
customerSelect.addEventListener("change", async () => {
  const customerId = customerSelect.value;
  if (!customerId || isNaN(customerId)) return;

  try {
    const response = await fetch(`${BASE_URL}/customers/${customerId}/meals`);
    const meals = await response.json();

    // Clear table
    customerMealsTable.innerHTML = "";

    meals.forEach((meal) => {
      const row = document.createElement("tr");
      row.innerHTML = `
        <td>${meal.food_name}</td>
        <td>${meal.brand}</td>
        <td>${meal.meal_date}</td>
      `;
      customerMealsTable.appendChild(row);
    });
  } catch (err) {
    console.error("Failed to load meals", err);
  }
});

// 3. Submit form to add a customer
addCustomerForm.addEventListener("submit", async (e) => {
  e.preventDefault();

  const firstName = document.getElementById("first_name").value;
  const lastName = document.getElementById("last_name").value;
  const birthDate = document.getElementById("birth_date").value;

  const newCustomer = {
    first_name: firstName,
    last_name: lastName,
    birth_date: birthDate,
  };

  try {
    const response = await fetch(`${BASE_URL}/customers`, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(newCustomer),
    });

    if (!response.ok) throw new Error("Failed to add customer");

    // Close modal (Bootstrap 5)
    const modal = bootstrap.Modal.getInstance(
      document.getElementById("add-customer-modal")
    );
    modal.hide();

    // Clear form fields
    addCustomerForm.reset();

    // Reload customer list
    await loadCustomers();
  } catch (err) {
    console.error("Failed to add customer", err);
  }
});

// Init
loadCustomers();

document.getElementById("navbar").innerHTML = `
  <nav style="
    background-color: darkgreen;
    padding: 15px;
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 20px;
  ">
    <a href="search_account.html" style="color: white; text-decoration: none; font-weight: bold;">Search Accounts</a>
    <a href="verify_customer.html?next=book_rental.html" style="color: white; text-decoration: none; font-weight: bold;">Book Rental</a>
    <a href="cancel_rental.html" style="color: white; text-decoration: none; font-weight: bold;">Cancel Rental</a>
    <a href="request_upgrades.html" style="color: white; text-decoration: none; font-weight: bold;">Request Upgrades</a>
    <a href="update_upgrades.html" style="color: white; text-decoration: none; font-weight: bold;">Update Upgrades</a>
    <a href="create_customer.html" style="color: white; text-decoration: none; font-weight: bold;">New Customer</a>
  </nav>
`;


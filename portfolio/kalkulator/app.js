// Function to insert number or operator into the input field
function insert(num) {
  document.getElementById("result").value += num;
}

// Function to clear the input field
function clearScreen() {
  document.getElementById("result").value = "";
}

// Function to delete the last character
function deleteLast() {
  let current = document.getElementById("result").value;
  document.getElementById("result").value = current.slice(0, -1);
}

// Function to calculate the input
function calculate() {
  let result = document.getElementById("result").value;

  // Check for percentage operator
  if (result.includes('%')) {
    const parts = result.split('%');
    if (parts.length === 2) {
      const base = parseFloat(parts[0]);
      const percent = parseFloat(parts[1]);
      // Calculate the percentage
      document.getElementById("result").value = (base * (percent / 100)).toString();
    } else {
      alert("Invalid percentage input.");
    }
  } else {
    if (result) {
      // Evaluate the expression safely
      try {
        document.getElementById("result").value = eval(result);
      } catch (e) {
        alert("Invalid expression.");
      }
    }
  }
}

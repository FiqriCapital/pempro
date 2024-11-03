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
    if (result) {
      document.getElementById("result").value = eval(result);
    }
  }
  
// Store the original image sources
const originalSrc1 = './public/images/button 1.svg';
const hoverSrc1 = './public/images/button 1 hover.png';
const originalSrc2 = './public/images/button 2.svg';
const hoverSrc2 = './public/images/button 2 hover.png';
const originalSrc3 = './public/images/button 3.svg';
const hoverSrc3 = './public/images/button 2 hover (1).png';
// Add event listeners for the first image
productImage1.addEventListener('mouseover', function() {
    this.src = hoverSrc1; // Change to hover image
});
productImage1.addEventListener('mouseout', function() {
    this.src = originalSrc1; // Change back to original image
});
// Add event listeners for the second image
productImage2.addEventListener('mouseover', function() {
    this.src = hoverSrc2; // Change to hover image
});
productImage2.addEventListener('mouseout', function() {
    this.src = originalSrc2; // Change back to original image
});
// Add event listeners for the third image
productImage3.addEventListener('mouseover', function() {
    this.src = hoverSrc3; // Change to hover image
});
productImage3.addEventListener('mouseout', function() {
    this.src = originalSrc3; // Change back to original image
});
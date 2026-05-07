// Get all the buttons and modal cards
const deleteBtn = document.getElementById('delete-product-btn');
const changePhotosBtn = document.querySelector('.change-photo-btn button');

const deleteConfirmationCard = document.querySelector(
  '.delete-confirmation-card',
);
const changeMediaCard = document.querySelector('.change-media-card');

const confirmDeleteBtn = document.getElementById('confirm-delete-btn');
const cancelDeleteBtn = document.getElementById('cancel-delete-btn');
const savePhotosBtn = document.getElementById('save-photos-btn');

// Function to get the popout card parent and toggle active state
function getPopoutCardParent(card) {
  return card.closest('.popout-card');
}

// Delete button click handler
deleteBtn.addEventListener('click', function () {
  const popoutCard = getPopoutCardParent(deleteConfirmationCard);
  popoutCard.classList.add('active');
});

// Cancel delete button click handler
cancelDeleteBtn.addEventListener('click', function () {
  const popoutCard = getPopoutCardParent(deleteConfirmationCard);
  popoutCard.classList.remove('active');
});

// Confirm delete button click handler
confirmDeleteBtn.addEventListener('click', function () {
  // Add your delete logic here
  console.log('Product deleted');
  const popoutCard = getPopoutCardParent(deleteConfirmationCard);
  popoutCard.classList.remove('active');
  // You can redirect or refresh the page here
});

// Change photos button click handler
changePhotosBtn.addEventListener('click', function () {
  const popoutCard = getPopoutCardParent(changeMediaCard);
  popoutCard.classList.add('active');
});

// Save photos button click handler
savePhotosBtn.addEventListener('click', function () {
  const fileInput = document.getElementById('photo-upload');
  if (fileInput.files.length === 0) {
    alert('Please select at least one photo');
    return;
  }

  // Add your photo upload logic here
  console.log('Photos saved:', fileInput.files);

  const popoutCard = getPopoutCardParent(changeMediaCard);
  popoutCard.classList.remove('active');
});

// Close modal when clicking outside the card
document.querySelectorAll('.popout-card').forEach((popoutCard) => {
  popoutCard.addEventListener('click', function (e) {
    // Close if clicking on the overlay (not on the card itself)
    if (e.target === this) {
      this.classList.remove('active');
    }
  });
});

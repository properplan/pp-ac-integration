/**
 * ProperPlan AC Email - Frontend "Send to AC" functionality
 */
(function () {
  'use strict';

  document.addEventListener('DOMContentLoaded', function () {
    var sendButton = document.getElementById('frontend-send-to-ac');
    if (!sendButton) return;

    var postId = sendButton.dataset.postId;
    if (!postId) {
      console.error('Post ID missing on button.');
      return;
    }

    sendButton.addEventListener('click', function () {
      console.log('Preparing to send:', { action: 'properplan_send_to_ac', postId: postId });
      sendButton.disabled = true;
      sendButton.textContent = 'Sending...';

      var ajaxUrl = (typeof properplan_ac_email !== 'undefined' && properplan_ac_email.ajaxurl)
        ? properplan_ac_email.ajaxurl
        : (window.ajaxurl || '/wp-admin/admin-ajax.php');

      var body = new URLSearchParams({
        action: 'properplan_send_to_ac',
        postId: postId
      }).toString();

      fetch(ajaxUrl, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: body
      })
        .then(function (response) {
          console.log('Raw response:', response);
          return response.text().then(function (text) {
            console.log('Raw response text:', text);
            try {
              return JSON.parse(text);
            } catch (e) {
              throw new Error('Invalid JSON response: ' + text);
            }
          });
        })
        .then(function (data) {
          console.log('Parsed response:', data);
          if (data && data.success) {
            alert('✅ Email sent to ActiveCampaign successfully!');
          } else {
            var message = (data && data.data && (data.data.message || data.data.error)) || (data && data.message) || 'Unknown error.';
            alert('❌ Failed to send email: ' + message);
            console.error('Failed to send email:', data);
          }
        })
        .catch(function (error) {
          console.error('Full error details:', error);
          alert('⚠️ An error occurred while sending the email. Please try again.');
        })
        .finally(function () {
          sendButton.disabled = false;
          sendButton.textContent = 'Send to AC';
        });
    });
  });
})();
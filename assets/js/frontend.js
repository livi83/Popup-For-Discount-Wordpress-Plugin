(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', function () {
       	const popup = document.getElementById('pfd-popup');
		const bar = document.getElementById('pfd-bar');
		const stickyBox = document.getElementById('pfd-sticky-box');
		const stickyButton = document.getElementById('pfd-sticky-button');
		const stickyClose = document.getElementById('pfd-sticky-close');
		const form = document.getElementById('pfd-form');
        const emailInput = document.getElementById('pfd-email');
        const closeButtons = document.querySelectorAll('.pfd-close');
        const barClose = document.querySelector('.pfd-bar-close');

        if (!popup || !bar || !stickyBox || !stickyButton || !stickyClose || !form || !emailInput) {
			return;
		}
		
		const data = typeof pfdData !== 'undefined' ? pfdData : {};

		const storageKey = data.storageKey || 'popup_for_discount_state';
        const campaignId = data.campaignId || '';
        const couponCode = data.couponCode || '';
        const popupDelay = parseInt(data.popupDelay, 10) || 1200;
        const stickyHideHours = parseInt(data.stickyHideHours, 10) || 24;

        function getState() {
            try {
                return JSON.parse(localStorage.getItem(storageKey)) || {};
            } catch (e) {
                return {};
            }
        }

        function setState(data) {
            const current = getState();

            localStorage.setItem(storageKey, JSON.stringify({
                ...current,
                ...data,
                campaignId: campaignId,
                couponCode: couponCode,
                updatedAt: new Date().toISOString()
            }));
        }
		
		function isStickyTemporarilyHidden(state) {
			const hiddenUntil = parseInt(state.stickyHiddenUntil, 10);

			if (!hiddenUntil) {
				return false;
			}

			return Date.now() < hiddenUntil;
		}

        function showPopup() {
            hideStickyButton();
            popup.style.setProperty('display', 'flex', 'important');
        }

        function showBar() {
            hideStickyButton();
            bar.style.setProperty('display', 'flex', 'important');
        }

        function showStickyButton() {
            stickyBox.style.setProperty('display', 'inline-flex', 'important');
        }

        function hidePopup() {
            popup.style.setProperty('display', 'none', 'important');
        }

        function hideBar(markClosed = true) {
            bar.style.setProperty('display', 'none', 'important');

            if (markClosed) {
                setState({
                    barClosed: true
                });
            }
        }

        function hideStickyButton() {
            stickyBox.style.setProperty('display', 'none', 'important');
        }

        function showStepTwo() {
            const stepOne = popup.querySelector('.pfd-step-1');
            const stepTwo = popup.querySelector('.pfd-step-2');

            if (stepOne) {
                stepOne.style.display = 'none';
            }

            if (stepTwo) {
                stepTwo.style.display = 'block';
            }

            setState({
                emailSubmitted: true,
                popupSeen: true,
                popupClosed: false,
                stickyButtonVisible: false
            });

            hideStickyButton();
        }
		
		function init() {
			let state = getState();

			const savedCampaignId = state.campaignId || '';
            const savedCouponCode = state.couponCode || '';

            if (
                (savedCampaignId && savedCampaignId !== campaignId) ||
                (savedCouponCode && savedCouponCode !== couponCode)
            ) {
                localStorage.removeItem(storageKey);
                state = {};
            }

			if (state.emailSubmitted) {
				hideStickyButton();

				if (!state.barClosed) {
					showBar();
				}

				return;
			}

			if (state.popupClosed) {
				if (state.stickyButtonVisible && !isStickyTemporarilyHidden(state)) {
					showStickyButton();
				}

				return;
			}

			window.setTimeout(showPopup, popupDelay);
		}
		
        form.addEventListener('submit', function (event) {
            event.preventDefault();

            const email = emailInput.value.trim();
			const honeypotInput = document.getElementById('pfd-website');
			const honeypotValue = honeypotInput ? honeypotInput.value.trim() : '';

            if (!email || !emailInput.checkValidity()) {
                emailInput.reportValidity();
                return;
            }

            const submitButton = form.querySelector('button[type="submit"]');
            const originalButtonText = submitButton ? submitButton.textContent : '';

            if (submitButton) {
                submitButton.disabled = true;
                submitButton.textContent = 'Saving...';
            }

            const formData = new FormData();
            formData.append('action', 'pfd_save_email');
            formData.append('nonce', data.nonce || '');
			formData.append('email', email);
            formData.append('campaign_id', campaignId);
            formData.append('coupon_code', couponCode);
            formData.append('page_url', window.location.href);
            formData.append('pfd_website', honeypotValue);

            fetch(data.ajaxUrl || '/wp-admin/admin-ajax.php', {
                method: 'POST',
                credentials: 'same-origin',
                body: formData
            })
                .then(function (response) {
                    return response.json();
                })
                .then(function (data) {
                    if (!data || !data.success) {
                        const message = data && data.data && data.data.message
                            ? data.data.message
                            : 'Email could not be saved.';

                        throw new Error(message);
                    }

                    setState({
                        email: email,
                        emailSubmitted: true,
                        popupSeen: true,
                        popupClosed: false,
                        barClosed: false
                    });

                    showStepTwo();
                })
                .catch(function (error) {
                    alert(error.message || 'Something went wrong. Please try again.');
                })
                .finally(function () {
                    if (submitButton) {
                        submitButton.disabled = false;
                        submitButton.textContent = originalButtonText;
                    }
                });
        });

        closeButtons.forEach(function (button) {
            button.addEventListener('click', function () {
                const state = getState();

                hidePopup();

                if (state.emailSubmitted) {
                    hideStickyButton();
                    showBar();

                    setState({
                        popupClosed: true,
                        popupSeen: true,
                        stickyButtonVisible: false
                    });

                    return;
                }

             hideBar(false);

			setState({
				popupClosed: true,
				popupSeen: true,
				stickyButtonVisible: true,
				stickyHiddenUntil: 0,
				barClosed: true
			});

			showStickyButton();
            });
        });

        if (barClose) {
            barClose.addEventListener('click', function () {
                hideBar(true);
            });
        }

        stickyButton.addEventListener('click', function () {
			hideStickyButton();
			hideBar(false);

			setState({
				popupClosed: false,
				stickyButtonVisible: false,
				stickyHiddenUntil: 0
			});

			showPopup();
		});
		
		stickyClose.addEventListener('click', function (event) {
			event.preventDefault();
			event.stopPropagation();

			const hiddenUntil = Date.now() + (stickyHideHours * 60 * 60 * 1000);

			hideStickyButton();

			setState({
				popupClosed: true,
				popupSeen: true,
				stickyButtonVisible: false,
				stickyHiddenUntil: hiddenUntil
			});
		});

        init();
    });
})();
/**
 * Confirmation Modal Utility
 * Provides easy-to-use functions for confirmation dialogs across the lecturer portal
 */

// Show confirmation modal
function showConfirmation(options) {
    const defaultOptions = {
        title: 'Confirm Action',
        message: 'Are you sure you want to proceed?',
        confirmText: 'Confirm',
        cancelText: 'Cancel',
        confirmClass: 'bg-red-600 hover:bg-red-700',
        onConfirm: null
    };
    
    const config = { ...defaultOptions, ...options };
    
    window.dispatchEvent(new CustomEvent('confirm-action', {
        detail: config
    }));
}

// Convenience functions for common confirmation types
const Confirmations = {
    // Delete confirmation
    delete: (itemName, onConfirm) => {
        showConfirmation({
            title: 'Delete Confirmation',
            message: `Are you sure you want to delete ${itemName}? This action cannot be undone.`,
            confirmText: 'Delete',
            confirmClass: 'bg-red-600 hover:bg-red-700',
            onConfirm
        });
    },
    
    // Deactivate confirmation
    deactivate: (itemName, onConfirm) => {
        showConfirmation({
            title: 'Deactivate Confirmation',
            message: `Are you sure you want to deactivate ${itemName}?`,
            confirmText: 'Deactivate',
            confirmClass: 'bg-yellow-600 hover:bg-yellow-700',
            onConfirm
        });
    },
    
    // Reset confirmation
    reset: (itemName, onConfirm) => {
        showConfirmation({
            title: 'Reset Confirmation',
            message: `Are you sure you want to reset ${itemName} to default settings? This action cannot be undone.`,
            confirmText: 'Reset',
            confirmClass: 'bg-orange-600 hover:bg-orange-700',
            onConfirm
        });
    },
    
    // Close session confirmation
    closeSession: (onConfirm) => {
        showConfirmation({
            title: 'Close Session',
            message: 'Are you sure you want to close this attendance session? Students will no longer be able to mark attendance.',
            confirmText: 'Close Session',
            confirmClass: 'bg-red-600 hover:bg-red-700',
            onConfirm
        });
    },
    
    // Regenerate code confirmation
    regenerateCode: (onConfirm) => {
        showConfirmation({
            title: 'Regenerate Code',
            message: 'Are you sure you want to regenerate the attendance code? The current code will become invalid.',
            confirmText: 'Regenerate',
            confirmClass: 'bg-blue-600 hover:bg-blue-700',
            onConfirm
        });
    },
    
    // Custom confirmation
    custom: (title, message, confirmText, confirmClass, onConfirm) => {
        showConfirmation({
            title,
            message,
            confirmText,
            confirmClass: confirmClass || 'bg-red-600 hover:bg-red-700',
            onConfirm
        });
    }
};

// Make Confirmations available globally
window.Confirmations = Confirmations;
window.showConfirmation = showConfirmation;


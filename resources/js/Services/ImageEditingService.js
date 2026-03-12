import Cropper from 'cropperjs';

class ImageEditingService {
    constructor() {
        this.cropper = null;
        this.currentImage = null;
        this.filters = {
            brightness: 0,
            contrast: 0,
            saturation: 0,
            hue: 0,
            blur: 0,
            sharpen: 0,
            sepia: 0,
            grayscale: 0,
            invert: 0,
            opacity: 100
        };
    }

    /**
     * Initialize cropper for image editing
     * @param {HTMLImageElement} imageElement 
     * @param {Object} options 
     * @returns {Cropper} cropper instance
     */
    initCropper(imageElement, options = {}) {
        // Destroy existing cropper if any
        if (this.cropper) {
            this.cropper.destroy();
        }

        const defaultOptions = {
            aspectRatio: NaN,
            viewMode: 1,
            dragMode: 'move',
            autoCropArea: 1,
            restore: false,
            guides: true,
            center: true,
            highlight: true,
            cropBoxMovable: true,
            cropBoxResizable: true,
            toggleDragModeOnDblclick: true,
            ready: () => {
                console.log('Cropper is ready');
            }
        };

        const cropperOptions = { ...defaultOptions, ...options };
        this.cropper = new Cropper(imageElement, cropperOptions);
        this.currentImage = imageElement;
        
        return this.cropper;
    }

    /**
     * Apply advanced filters to image
     * @param {fabric.Image} fabricImage 
     * @param {Object} filterSettings 
     */
    applyFilters(fabricImage, filterSettings) {
        if (!fabricImage) return;

        // Update internal filter state
        this.filters = { ...this.filters, ...filterSettings };

        // Clear existing filters
        fabricImage.filters = [];

        // Apply brightness filter
        if (this.filters.brightness !== 0) {
            fabricImage.filters.push(new fabric.Image.filters.Brightness({
                brightness: this.filters.brightness / 100
            }));
        }

        // Apply contrast filter
        if (this.filters.contrast !== 0) {
            fabricImage.filters.push(new fabric.Image.filters.Contrast({
                contrast: this.filters.contrast / 100
            }));
        }

        // Apply saturation filter
        if (this.filters.saturation !== 0) {
            fabricImage.filters.push(new fabric.Image.filters.Saturation({
                saturation: this.filters.saturation / 100
            }));
        }

        // Apply hue rotation
        if (this.filters.hue !== 0) {
            fabricImage.filters.push(new fabric.Image.filters.HueRotation({
                rotation: this.filters.hue * Math.PI / 180
            }));
        }

        // Apply blur
        if (this.filters.blur > 0) {
            fabricImage.filters.push(new fabric.Image.filters.Blur({
                blur: this.filters.blur / 10
            }));
        }

        // Apply sharpen
        if (this.filters.sharpen > 0) {
            fabricImage.filters.push(new fabric.Image.filters.Convolute({
                matrix: [
                    0, -this.filters.sharpen/10, 0,
                    -this.filters.sharpen/10, 1 + 4*this.filters.sharpen/10, -this.filters.sharpen/10,
                    0, -this.filters.sharpen/10, 0
                ]
            }));
        }

        // Apply sepia
        if (this.filters.sepia > 0) {
            fabricImage.filters.push(new fabric.Image.filters.Sepia({
                intensity: this.filters.sepia / 100
            }));
        }

        // Apply grayscale
        if (this.filters.grayscale > 0) {
            fabricImage.filters.push(new fabric.Image.filters.Grayscale({
                mode: 'average'
            }));
        }

        // Apply invert
        if (this.filters.invert > 0) {
            fabricImage.filters.push(new fabric.Image.filters.Invert());
        }

        // Apply all filters
        fabricImage.applyFilters();
    }

    /**
     * Get current filter settings
     * @returns {Object}
     */
    getFilters() {
        return { ...this.filters };
    }

    /**
     * Reset all filters
     * @param {fabric.Image} fabricImage 
     */
    resetFilters(fabricImage) {
        this.filters = {
            brightness: 0,
            contrast: 0,
            saturation: 0,
            hue: 0,
            blur: 0,
            sharpen: 0,
            sepia: 0,
            grayscale: 0,
            invert: 0,
            opacity: 100
        };

        if (fabricImage) {
            fabricImage.filters = [];
            fabricImage.applyFilters();
        }
    }

    /**
     * Crop image using cropper
     * @returns {Promise<string>} cropped image data URL
     */
    getCroppedImageData() {
        return new Promise((resolve, reject) => {
            if (!this.cropper) {
                reject(new Error('Cropper not initialized'));
                return;
            }

            try {
                // Check if getCroppedCanvas method exists
                if (typeof this.cropper.getCroppedCanvas !== 'function') {
                    reject(new Error('getCroppedCanvas method not available'));
                    return;
                }
                
                const croppedCanvas = this.cropper.getCroppedCanvas({
                    maxWidth: 4096,
                    maxHeight: 4096,
                    fillColor: '#fff',
                    imageSmoothingEnabled: true,
                    imageSmoothingQuality: 'high'
                });

                if (!croppedCanvas) {
                    reject(new Error('Failed to create cropped canvas'));
                    return;
                }

                const dataUrl = croppedCanvas.toDataURL('image/png');
                resolve(dataUrl);
            } catch (error) {
                reject(error);
            }
        });
    }

    /**
     * Rotate image
     * @param {fabric.Image} fabricImage 
     * @param {number} angle - degrees to rotate
     */
    rotateImage(fabricImage, angle) {
        if (!fabricImage) return;
        
        const currentAngle = fabricImage.angle || 0;
        fabricImage.set('angle', currentAngle + angle);
        fabricImage.setCoords();
    }

    /**
     * Flip image horizontally
     * @param {fabric.Image} fabricImage 
     */
    flipHorizontal(fabricImage) {
        if (!fabricImage) return;
        
        const scaleX = fabricImage.scaleX || 1;
        fabricImage.set('scaleX', -scaleX);
        fabricImage.setCoords();
    }

    /**
     * Flip image vertically
     * @param {fabric.Image} fabricImage 
     */
    flipVertical(fabricImage) {
        if (!fabricImage) return;
        
        const scaleY = fabricImage.scaleY || 1;
        fabricImage.set('scaleY', -scaleY);
        fabricImage.setCoords();
    }

    /**
     * Adjust image opacity
     * @param {fabric.Image} fabricImage 
     * @param {number} opacity - 0 to 100
     */
    setOpacity(fabricImage, opacity) {
        if (!fabricImage) return;
        
        this.filters.opacity = opacity;
        fabricImage.set('opacity', opacity / 100);
    }

    /**
     * Apply artistic effects
     * @param {fabric.Image} fabricImage 
     * @param {string} effect - 'vintage', 'lomo', 'clarity', 'sinCity', 'sunrise', 'crossProcess'
     */
    applyArtisticEffect(fabricImage, effect) {
        if (!fabricImage) return;

        // Clear existing filters first
        fabricImage.filters = [];

        switch (effect) {
            case 'vintage':
                fabricImage.filters.push(
                    new fabric.Image.filters.Brightness({ brightness: 0.1 }),
                    new fabric.Image.filters.Contrast({ contrast: 0.15 }),
                    new fabric.Image.filters.Sepia({ intensity: 0.2 }),
                    new fabric.Image.filters.HueRotation({ rotation: 5 * Math.PI / 180 })
                );
                break;
                
            case 'lomo':
                fabricImage.filters.push(
                    new fabric.Image.filters.Brightness({ brightness: 0.15 }),
                    new fabric.Image.filters.Contrast({ contrast: 0.2 }),
                    new fabric.Image.filters.Saturation({ saturation: 0.2 }),
                    new fabric.Image.filters.Vibrance({ vibrance: 0.3 })
                );
                break;
                
            case 'clarity':
                fabricImage.filters.push(
                    new fabric.Image.filters.Sharpen(),
                    new fabric.Image.filters.Contrast({ contrast: 0.1 }),
                    new fabric.Image.filters.Brightness({ brightness: 0.05 })
                );
                break;
                
            case 'sinCity':
                fabricImage.filters.push(
                    new fabric.Image.filters.Grayscale(),
                    new fabric.Image.filters.Contrast({ contrast: 0.25 }),
                    new fabric.Image.filters.Brightness({ brightness: 0.1 })
                );
                break;
                
            case 'sunrise':
                fabricImage.filters.push(
                    new fabric.Image.filters.Warmth({ warmth: 0.3 }),
                    new fabric.Image.filters.Brightness({ brightness: 0.1 }),
                    new fabric.Image.filters.Saturation({ saturation: 0.15 })
                );
                break;
                
            case 'crossProcess':
                fabricImage.filters.push(
                    new fabric.Image.filters.ColorMatrix({
                        matrix: [
                            1.2, -0.2, 0.2, 0, 0,
                            -0.2, 1.2, -0.2, 0, 0,
                            0.2, -0.2, 1.2, 0, 0,
                            0, 0, 0, 1, 0
                        ]
                    })
                );
                break;
        }

        fabricImage.applyFilters();
    }

    /**
     * Destroy cropper instance
     */
    destroy() {
        if (this.cropper) {
            this.cropper.destroy();
            this.cropper = null;
        }
        this.currentImage = null;
    }

    /**
     * Get image dimensions
     * @param {fabric.Image} fabricImage 
     * @returns {Object} width and height
     */
    getImageDimensions(fabricImage) {
        if (!fabricImage) return { width: 0, height: 0 };
        
        return {
            width: fabricImage.width * (fabricImage.scaleX || 1),
            height: fabricImage.height * (fabricImage.scaleY || 1)
        };
    }

    /**
     * Resize image proportionally
     * @param {fabric.Image} fabricImage 
     * @param {number} newWidth 
     * @param {number} newHeight 
     */
    resizeImage(fabricImage, newWidth, newHeight) {
        if (!fabricImage) return;
        
        const currentDimensions = this.getImageDimensions(fabricImage);
        const scaleX = newWidth / currentDimensions.width;
        const scaleY = newHeight / currentDimensions.height;
        
        fabricImage.set({
            scaleX: scaleX,
            scaleY: scaleY
        });
        fabricImage.setCoords();
    }
}

// Export singleton instance
export default new ImageEditingService();
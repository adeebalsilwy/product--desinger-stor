/**
 * Advanced Designer Features - Canva-like Functionality
 * Shared composable functions for transformation, selection, and manipulation
 */

import { ref } from 'vue';

export function useAdvancedDesigner() {
  const designElements = ref([]);
  const selectedElement = ref(null);
  const activeHandle = ref(null);
  const isDragging = ref(false);
  const isResizing = ref(false);
  const isRotating = ref(false);
  const dragStartPos = ref({ x: 0, y: 0 });
  const initialElementState = ref(null);
  const elementCounter = ref(0);

  const selectElement = (element) => {
    designElements.value.forEach(el => el.selected = false);
    
    if (element) {
      element.selected = true;
      selectedElement.value = element;
    } else {
      selectedElement.value = null;
    }
  };

  const isPointInElement = (point, element) => {
    const centerX = element.x + element.width / 2;
    const centerY = element.y + element.height / 2;
    
    const rotatedX = (point.x - centerX) * Math.cos(-element.rotation * Math.PI / 180) - 
                     (point.y - centerY) * Math.sin(-element.rotation * Math.PI / 180) + centerX;
    const rotatedY = (point.x - centerX) * Math.sin(-element.rotation * Math.PI / 180) + 
                     (point.y - centerY) * Math.cos(-element.rotation * Math.PI / 180) + centerY;
    
    return rotatedX >= element.x && 
           rotatedX <= element.x + element.width &&
           rotatedY >= element.y && 
           rotatedY <= element.y + element.height;
  };

  const findElementAtPoint = (point) => {
    for (let i = designElements.value.length - 1; i >= 0; i--) {
      if (isPointInElement(point, designElements.value[i])) {
        return designElements.value[i];
      }
    }
    return null;
  };

  const getResizeHandle = (x, y, element) => {
    if (!element) return null;
    
    const handleSize = 10;
    const corners = [
      { name: 'resize-tl', x: element.x, y: element.y },
      { name: 'resize-tr', x: element.x + element.width, y: element.y },
      { name: 'resize-bl', x: element.x, y: element.y + element.height },
      { name: 'resize-br', x: element.x + element.width, y: element.y + element.height },
    ];
    
    for (const corner of corners) {
      const dx = x - corner.x;
      const dy = y - corner.y;
      if (dx >= -handleSize && dx <= handleSize && dy >= -handleSize && dy <= handleSize) {
        return corner.name;
      }
    }
    
    const rotationHandleY = element.y - 30;
    const centerX = element.x + element.width / 2;
    const rotDx = x - centerX;
    const rotDy = y - rotationHandleY;
    if (rotDx >= -handleSize && rotDx <= handleSize && rotDy >= -handleSize && rotDy <= handleSize) {
      return 'rotate';
    }
    
    return null;
  };

  const handleMouseDown = (e, drawingCanvas) => {
    const rect = drawingCanvas.getBoundingClientRect();
    const mouseX = e.clientX - rect.left;
    const mouseY = e.clientY - rect.top;
    
    if (selectedElement.value) {
      const resizeHandle = getResizeHandle(mouseX, mouseY, selectedElement.value);
      
      if (resizeHandle === 'rotate') {
        isRotating.value = true;
        activeHandle.value = 'rotate';
        return;
      }
      
      if (['resize-tl', 'resize-tr', 'resize-bl', 'resize-br'].includes(resizeHandle)) {
        isResizing.value = true;
        activeHandle.value = resizeHandle;
        initialElementState.value = { ...selectedElement.value };
        dragStartPos.value = { x: mouseX, y: mouseY };
        return;
      }
    }
    
    const clickedElement = findElementAtPoint({ x: mouseX, y: mouseY });
    
    if (clickedElement) {
      selectElement(clickedElement);
      isDragging.value = true;
      activeHandle.value = 'drag';
      dragStartPos.value = { x: mouseX, y: mouseY };
      initialElementState.value = { ...clickedElement };
    } else {
      selectElement(null);
    }
  };

  const handleMouseMove = (e, drawingCanvas) => {
    if (!isDragging.value && !isResizing.value && !isRotating.value) return;
    
    const rect = drawingCanvas.getBoundingClientRect();
    const mouseX = e.clientX - rect.left;
    const mouseY = e.clientY - rect.top;
    
    if (isRotating.value && selectedElement.value) {
      const centerX = selectedElement.value.x + selectedElement.value.width / 2;
      const centerY = selectedElement.value.y + selectedElement.value.height / 2;
      const angle = Math.atan2(mouseY - centerY, mouseX - centerX);
      selectedElement.value.rotation = angle * 180 / Math.PI + 90;
      return true;
    }
    
    if (isResizing.value && selectedElement.value) {
      const deltaX = mouseX - dragStartPos.value.x;
      const deltaY = mouseY - dragStartPos.value.y;
      const initial = initialElementState.value;
      
      switch(activeHandle.value) {
        case 'resize-tl':
          selectedElement.value.x = initial.x + deltaX;
          selectedElement.value.y = initial.y + deltaY;
          selectedElement.value.width = Math.max(20, initial.width - deltaX);
          selectedElement.value.height = Math.max(20, initial.height - deltaY);
          break;
        case 'resize-tr':
          selectedElement.value.x = initial.x;
          selectedElement.value.y = initial.y + deltaY;
          selectedElement.value.width = Math.max(20, initial.width + deltaX);
          selectedElement.value.height = Math.max(20, initial.height - deltaY);
          break;
        case 'resize-bl':
          selectedElement.value.x = initial.x + deltaX;
          selectedElement.value.y = initial.y;
          selectedElement.value.width = Math.max(20, initial.width - deltaX);
          selectedElement.value.height = Math.max(20, initial.height + deltaY);
          break;
        case 'resize-br':
          selectedElement.value.x = initial.x;
          selectedElement.value.y = initial.y;
          selectedElement.value.width = Math.max(20, initial.width + deltaX);
          selectedElement.value.height = Math.max(20, initial.height + deltaY);
          break;
      }
      return true;
    }
    
    if (isDragging.value && selectedElement.value) {
      const deltaX = mouseX - dragStartPos.value.x;
      const deltaY = mouseY - dragStartPos.value.y;
      selectedElement.value.x = initialElementState.value.x + deltaX;
      selectedElement.value.y = initialElementState.value.y + deltaY;
      return true;
    }
    
    return false;
  };

  const handleMouseUp = () => {
    isDragging.value = false;
    isResizing.value = false;
    isRotating.value = false;
    activeHandle.value = null;
  };

  const deleteSelectedElement = () => {
    if (selectedElement.value) {
      designElements.value = designElements.value.filter(el => el !== selectedElement.value);
      selectElement(null);
    }
  };

  const handleKeyDown = (e) => {
    if (e.key === 'Delete' || e.key === 'Backspace') {
      deleteSelectedElement();
    }
  };

  return {
    designElements,
    selectedElement,
    activeHandle,
    isDragging,
    isResizing,
    isRotating,
    dragStartPos,
    initialElementState,
    elementCounter,
    selectElement,
    isPointInElement,
    findElementAtPoint,
    getResizeHandle,
    handleMouseDown,
    handleMouseMove,
    handleMouseUp,
    deleteSelectedElement,
    handleKeyDown,
  };
}

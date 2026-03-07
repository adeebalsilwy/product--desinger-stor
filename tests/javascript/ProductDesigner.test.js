import { mount, createLocalVue } from '@vue/test-utils';
import ProductDesigner from '@/Components/Designer/ProductDesigner.vue';
import { fabric } from 'fabric';
import axios from 'axios';

// Mock axios
jest.mock('axios');

describe('ProductDesigner Component', () => {
  let wrapper;
  let mockCanvas;

  beforeEach(() => {
    // Mock fabric canvas
    mockCanvas = {
      add: jest.fn(),
      setActiveObject: jest.fn(),
      toJSON: jest.fn().mockReturnValue({ objects: [] }),
      loadFromJSON: jest.fn().mockImplementation((json, callback) => {
        if (callback) callback();
      }),
      renderAll: jest.fn(),
      dispose: jest.fn(),
      on: jest.fn(),
      bringForward: jest.fn(),
      sendBackwards: jest.fn(),
      remove: jest.fn(),
      setCoords: jest.fn(),
    };

    global.fabric = {
      Canvas: jest.fn().mockImplementation(() => mockCanvas),
      IText: jest.fn().mockImplementation((text, options) => ({
        ...options,
        text,
        setCoords: jest.fn(),
      })),
      Image: {
        fromURL: jest.fn().mockImplementation((url, callback) => {
          callback({
            set: jest.fn(),
          });
        }),
      },
    };

    // Mock canvas element ref
    HTMLCanvasElement.prototype.getContext = jest.fn();

    // Mock file reader
    window.FileReader = jest.fn().mockImplementation(() => {
      return {
        readAsDataURL: jest.fn(),
        onload: jest.fn(),
      };
    });

    wrapper = mount(ProductDesigner, {
      propsData: {
        productTypeId: 1,
        initialDesign: null,
      },
    });
  });

  afterEach(() => {
    if (wrapper) {
      wrapper.destroy();
    }
  });

  it('renders correctly', () => {
    expect(wrapper.exists()).toBe(true);
    expect(wrapper.find('canvas').exists()).toBe(true);
  });

  it('initializes canvas on mount', () => {
    expect(global.fabric.Canvas).toHaveBeenCalledWith(
      expect.any(Object),
      expect.objectContaining({
        width: 800,
        height: 800,
        backgroundColor: '#ffffff',
      })
    );
  });

  it('adds text to canvas', async () => {
    const addTextSpy = jest.spyOn(mockCanvas, 'add');
    const setActiveObjectSpy = jest.spyOn(mockCanvas, 'setActiveObject');

    await wrapper.vm.addText();

    expect(addTextSpy).toHaveBeenCalled();
    expect(setActiveObjectSpy).toHaveBeenCalled();
    expect(wrapper.vm.isDirty).toBe(true);
  });

  it('handles image upload', async () => {
    const file = new File(['dummy content'], 'test.jpg', { type: 'image/jpeg' });
    const mockUrl = 'data:image/jpeg;base64,dummy';
    
    // Mock file reader onload
    const fileReaderConstructor = window.FileReader.mock.instances[0];
    fileReaderConstructor.onload = jest.fn();
    
    // Simulate file input change
    const input = wrapper.find('input[type="file"]');
    await input.setValue(file);

    // Trigger the onload callback manually
    fileReaderConstructor.onload({ target: { result: mockUrl } });

    expect(mockCanvas.add).toHaveBeenCalled();
  });

  it('adds clipart to canvas', async () => {
    const clipart = {
      id: 1,
      image_url: 'http://example.com/clipart.png',
      title: 'Test Clipart',
    };

    await wrapper.vm.addClipart(clipart);

    expect(global.fabric.Image.fromURL).toHaveBeenCalledWith(
      clipart.image_url,
      expect.any(Function),
      expect.objectContaining({ crossOrigin: 'anonymous' })
    );
  });

  it('updates canvas when object is modified', async () => {
    const mockObject = {
      setCoords: jest.fn(),
    };

    wrapper.vm.selectedObject = mockObject;
    await wrapper.vm.updateCanvas();

    expect(mockObject.setCoords).toHaveBeenCalled();
    expect(mockCanvas.renderAll).toHaveBeenCalled();
    expect(wrapper.vm.isDirty).toBe(true);
  });

  it('brings object forward', async () => {
    const mockObject = {};
    wrapper.vm.selectedObject = mockObject;
    
    await wrapper.vm.bringForward();

    expect(mockCanvas.bringForward).toHaveBeenCalledWith(mockObject);
    expect(wrapper.vm.isDirty).toBe(true);
  });

  it('sends object backwards', async () => {
    const mockObject = {};
    wrapper.vm.selectedObject = mockObject;
    
    await wrapper.vm.sendBackwards();

    expect(mockCanvas.sendBackwards).toHaveBeenCalledWith(mockObject);
    expect(wrapper.vm.isDirty).toBe(true);
  });

  it('deletes selected object', async () => {
    const mockObject = {};
    wrapper.vm.selectedObject = mockObject;
    
    await wrapper.vm.deleteObject();

    expect(mockCanvas.remove).toHaveBeenCalledWith(mockObject);
    expect(wrapper.vm.selectedObject).toBeNull();
    expect(wrapper.vm.isDirty).toBe(true);
  });

  it('loads assets from API', async () => {
    const mockFonts = {
      data: {
        data: [{ name: 'Arial' }, { name: 'Times New Roman' }],
      },
    };
    const mockCliparts = {
      data: {
        data: [
          { id: 1, image_url: 'url1', title: 'Clipart 1' },
          { id: 2, image_url: 'url2', title: 'Clipart 2' },
        ],
      },
    };

    axios.get
      .mockResolvedValueOnce(mockFonts)
      .mockResolvedValueOnce(mockCliparts);

    await wrapper.vm.loadAssets();

    expect(wrapper.vm.fonts).toEqual(['Arial', 'Times New Roman']);
    expect(wrapper.vm.cliparts).toEqual([
      { id: 1, image_url: 'url1', title: 'Clipart 1' },
      { id: 2, image_url: 'url2', title: 'Clipart 2' },
    ]);
  });

  it('saves design via API', async () => {
    const mockResponse = {
      data: {
        data: {
          id: 123,
          name: 'Test Design',
        },
        message: 'Design saved successfully',
      },
    };

    axios.post.mockResolvedValue(mockResponse);
    wrapper.vm.currentDesignId = null;

    await wrapper.vm.saveDesign();

    expect(axios.post).toHaveBeenCalledWith('/api/designs', {
      product_type_id: 1,
      name: 'My Design',
      design_data: { objects: [] },
    });
    expect(wrapper.vm.currentDesignId).toBe(123);
    expect(wrapper.vm.isDirty).toBe(false);
  });

  it('exports design via API', async () => {
    const mockResponse = {
      data: {
        data: {
          url: 'http://example.com/export.png',
        },
      },
    };

    axios.post.mockResolvedValue(mockResponse);
    wrapper.vm.currentDesignId = 123;

    // Mock window.open
    const openSpy = jest.spyOn(window, 'open').mockImplementation(() => {});

    await wrapper.vm.exportDesign();

    expect(axios.post).toHaveBeenCalledWith('/api/designs/123/export', {
      format: 'high_res',
    });
    expect(openSpy).toHaveBeenCalledWith('http://example.com/export.png', '_blank');

    openSpy.mockRestore();
  });

  it('emits events when design changes', async () => {
    const changedSpy = jest.spyOn(wrapper.vm, '$emit');
    
    await wrapper.vm.addText();
    
    expect(changedSpy).toHaveBeenCalledWith('changed');
  });

  it('emits saved event when design is saved', async () => {
    const mockResponse = {
      data: {
        data: { id: 123 },
      },
    };

    axios.post.mockResolvedValue(mockResponse);
    wrapper.vm.currentDesignId = null;
    
    const savedSpy = jest.spyOn(wrapper.vm, '$emit');
    
    await wrapper.vm.saveDesign();
    
    expect(savedSpy).toHaveBeenCalledWith('saved', { id: 123 });
  });
});
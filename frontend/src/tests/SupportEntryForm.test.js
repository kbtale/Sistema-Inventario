// @ts-nocheck
import { render, fireEvent, waitFor } from '@testing-library/svelte';
import { expect, test, vi, beforeEach } from 'vitest';
import SupportEntryForm from '../lib/SupportEntryForm.svelte';
import { api } from '../lib/api';

// Mock the API module
vi.mock('../lib/api', () => ({
  api: {
    getHardware: vi.fn(),
    getTelefonos: vi.fn(),
    getEntradas: vi.fn(),
    getUsuarios: vi.fn(),
    uploadPhoto: vi.fn(),
    supportEntry: vi.fn()
  }
}));

// Mock FileReader for visual auditing tests
const readerMock = {
  readAsDataURL: vi.fn(function() {
    this.onload({ target: { result: 'data:image/png;base64,test-image' } });
  })
};
vi.stubGlobal('FileReader', vi.fn(() => readerMock));

beforeEach(() => {
  vi.clearAllMocks();
  api.getHardware.mockResolvedValue([]);
  api.getTelefonos.mockResolvedValue([]);
  api.getEntradas.mockResolvedValue([]);
  api.getUsuarios.mockResolvedValue([]);
});

test('SupportEntryForm enforces mandatory fields', async () => {
  const { getByText, findByText } = render(SupportEntryForm, { props: {} });
  
  const submitButton = getByText('Start Maintenance');
  await fireEvent.click(submitButton);

  const error = await findByText('Please fill in all required fields.');
  expect(error).toBeDefined();
});

test('Successful data loading updates Select options', async () => {
  api.getUsuarios.mockResolvedValue([{ id: 1, name: 'John Tech' }]);
  api.getHardware.mockResolvedValue([{ 
    id_hardware: 101, 
    tipo_hardware: 'Laptop', 
    marca_hardware: 'Dell', 
    modelo_hardware: 'XPS', 
    estado_actual_unidad: 1 
  }]);

  const { getByLabelText, findByText } = render(SupportEntryForm, { props: {} });

  // Wait for loading to finish
  const assetSelect = getByLabelText('Select Asset to Support');
  await waitFor(() => expect(assetSelect).not.toBeDisabled());

  const techSelect = getByLabelText('Assigned Technician');
  await findByText('John Tech');
  
  expect(assetSelect.innerHTML).toContain('Dell XPS');
});

test('Visual Auditing: photo selection updates preview', async () => {
  const { getByLabelText, findByAltText } = render(SupportEntryForm, { props: {} });
  
  const file = new File(['test'], 'audit.png', { type: 'image/png' });
  const input = getByLabelText('Capture Condition Photo');
  
  await fireEvent.change(input, { target: { files: [file] } });
  
  const preview = await findByAltText('Audit preview');
  expect(preview.src).toContain('data:image/png;base64,test-image');
});

test('Successful submission resets form state', async () => {
  api.getHardware.mockResolvedValue([{ id_hardware: 1, tipo_hardware: 'HW', estado_actual_unidad: 1 }]);
  api.getUsuarios.mockResolvedValue([{ id: 1, name: 'Tech' }]);
  api.supportEntry.mockResolvedValue({ success: true });

  const { getByLabelText, getByText, findByText } = render(SupportEntryForm, { props: {} });

  await waitFor(() => expect(getByLabelText('Select Asset to Support')).not.toBeDisabled());

  // Fill required fields
  await fireEvent.change(getByLabelText('Select Asset to Support'), { target: { value: '1' } });
  await fireEvent.change(getByLabelText('Assigned Technician'), { target: { value: '1' } });
  await fireEvent.input(getByLabelText('Work Order Number'), { target: { value: 'ORD-TEST-001' } });

  const submitButton = getByText('Start Maintenance');
  await fireEvent.click(submitButton);

  await findByText('Asset checked into support successfully!');
  expect(getByLabelText('Work Order Number').value).toBe('');
});

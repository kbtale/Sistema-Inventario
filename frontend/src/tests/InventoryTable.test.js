// @ts-nocheck
import { render, fireEvent, waitFor } from '@testing-library/svelte';
import { expect, test, vi, beforeEach } from 'vitest';
import InventoryTable from '../lib/InventoryTable.svelte';
import { api } from '../lib/api';

// Mock the API module
vi.mock('../lib/api', () => ({
  api: {
    getHardware: vi.fn().mockResolvedValue([])
  }
}));

const mockAssets = [
  { 
    id_hardware: 1, 
    tipo_hardware: 'Laptop', 
    marca_hardware: 'Dell', 
    modelo_hardware: 'Latitude', 
    bienes_hardware: 'TAG-001', 
    usuario_hardware: 'Alice',
    fecha_entrada: '2024-01-01',
    pulse_score: 85
  },
  { 
    id_hardware: 2, 
    tipo_hardware: 'Desktop', 
    marca_hardware: 'HP', 
    modelo_hardware: 'EliteDesk', 
    bienes_hardware: 'TAG-002', 
    usuario_hardware: 'Bob',
    fecha_entrada: '2024-01-02',
    pulse_score: 45
  }
];

beforeEach(() => {
  vi.clearAllMocks();
});

test('InventoryTable renders asset rows from API', async () => {
  api.getHardware.mockResolvedValue(mockAssets);

  const { findByText, getAllByRole } = render(InventoryTable, { props: {} });

  // Verify rendering of asset tags
  expect(await findByText('TAG-001')).toBeDefined();
  expect(await findByText('TAG-002')).toBeDefined();

  // Check row count (excluding header)
  const rows = getAllByRole('row');
  expect(rows.length).toBe(3); // 1 header + 2 data rows
});

test('InventoryTable searches assets correctly', async () => {
  api.getHardware.mockResolvedValue(mockAssets);

  const { getByPlaceholderText, queryByText, findByText } = render(InventoryTable, { props: {} });

  await waitFor(() => expect(queryByText('TAG-001')).toBeDefined());

  const searchInput = getByPlaceholderText('Search by tag, user, or model...');
  await fireEvent.input(searchInput, { target: { value: 'Dell' } });

  expect(queryByText('Dell')).toBeDefined();
  expect(queryByText('HP')).toBeNull();
});

test('InventoryTable filters by category correctly', async () => {
  api.getHardware.mockResolvedValue(mockAssets);

  const { getByRole, queryByText } = render(InventoryTable, { props: {} });

  await waitFor(() => expect(queryByText('Laptop')).toBeDefined());

  const select = getByRole('combobox');
  await fireEvent.change(select, { target: { value: 'Desktop' } });

  expect(queryByText('Desktop')).toBeDefined();
  expect(queryByText('Laptop')).toBeNull();
});

test('InventoryTable respects external Sede filter', async () => {
    // Mock assets with different sedes
    const sedeAssets = [
        { ...mockAssets[0], id_sede: 1, bienes_hardware: 'SEDE1-TAG' },
        { ...mockAssets[1], id_sede: 2, bienes_hardware: 'SEDE2-TAG' }
    ];
    api.getHardware.mockResolvedValue(sedeAssets);

    const { queryByText } = render(InventoryTable, { props: { filterSede: 1 } });

    await waitFor(() => expect(queryByText('SEDE1-TAG')).toBeDefined());
    expect(queryByText('SEDE2-TAG')).toBeNull();
});

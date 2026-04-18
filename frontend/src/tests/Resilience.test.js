// @ts-nocheck
import { render, fireEvent } from '@testing-library/svelte';
import { expect, test, vi } from 'vitest';
import ErrorBoundary from '../lib/ErrorBoundary.svelte';
import { svelteCompile } from 'svelte/compiler';

// A mock component that crashes
const CrashingComponent = {
  render: () => { throw new Error('Simulated Crash'); }
};

test('ErrorBoundary traps errors and displays fallback UI', async () => {
  // Suppress console.error for this expected crash
  const consoleSpy = vi.spyOn(console, 'error').mockImplementation(() => {});
  
  const { getByText, queryByText } = render(ErrorBoundary, { 
    props: {
      children: () => { throw new Error('Simulated Crash'); }
    }
  });

  // Manually trigger the error listener if JSDOM doesn't bubble it automatically
  window.dispatchEvent(new ErrorEvent('error', {
    error: new Error('Simulated Crash'),
    message: 'Simulated Crash'
  }));

  expect(await getByText('Component Error')).toBeDefined();
  expect(await getByText('Simulated Crash')).toBeDefined();
  
  consoleSpy.mockRestore();
});

test('ErrorBoundary reset mechanism clears errors', async () => {
  const { getByText, queryByText, rerender } = render(ErrorBoundary, { 
    props: {
      children: () => { throw new Error('Simulated Crash'); }
    }
  });

  window.dispatchEvent(new ErrorEvent('error', {
    error: new Error('Simulated Crash')
  }));

  const resetButton = getByText('Try again');
  await fireEvent.click(resetButton);

  expect(queryByText('Component Error')).toBeNull();
});

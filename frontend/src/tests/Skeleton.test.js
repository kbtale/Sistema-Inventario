import { render } from '@testing-library/svelte';
import { expect, test } from 'vitest';
import Skeleton from '../lib/Skeleton.svelte';

test('Skeleton renders with default properties', () => {
  const { container } = render(Skeleton);
  const skeleton = container.querySelector('.skeleton-loading');
  
  expect(skeleton).toBeDefined();
  expect(skeleton).toHaveStyle({ width: '100%', height: '1rem' });
});

test('Skeleton applies custom dimensions correctly', () => {
  const { container } = render(Skeleton, { width: '50%', height: '20px' });
  const skeleton = container.querySelector('.skeleton-loading');
  
  expect(skeleton).toHaveStyle({ width: '50%', height: '20px' });
});

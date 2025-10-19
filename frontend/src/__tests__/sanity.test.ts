import { describe, it, expect } from 'vitest'

describe('Sanity Check', () => {
  it('should pass basic math test', () => {
    expect(1 + 1).toBe(2)
  })

  it('should handle string operations', () => {
    const greeting = 'Hello'
    const name = 'World'
    expect(`${greeting} ${name}`).toBe('Hello World')
  })

  it('should work with arrays', () => {
    const numbers = [1, 2, 3, 4, 5]
    expect(numbers.length).toBe(5)
    expect(numbers).toContain(3)
  })
})

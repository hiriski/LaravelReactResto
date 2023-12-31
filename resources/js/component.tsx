import { useState } from 'react'

const Component = () => {
  const [count, setCount] = useState(0)
  return (
    <div>
      <p>Hello i am a react component : {count}</p>
      <button onClick={() => setCount(prev => prev + 1)}>Increment</button>
    </div>
  )
}

export default Component

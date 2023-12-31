// import './bootstrap';

import ReactDOM from 'react-dom/client'
import { BrowserRouter } from 'react-router-dom'
import Component from './component'
import '../css/app.css'

ReactDOM.createRoot(document.getElementById('app')!).render(
  <BrowserRouter>
    <Component />
  </BrowserRouter>
)

import React from 'react';
import ReactDOM from 'react-dom';
import './index.css';
import App from './App';
import reportWebVitals from './reportWebVitals';

const reactAppData = window.gpBriefPlugin || {'appSelector': '#root'};
const { appSelector, token } = reactAppData;
const appAnchorElement = document.querySelector(appSelector);
console.log(appAnchorElement);

ReactDOM.render(
  <React.StrictMode>
    <App token={token}/>
  </React.StrictMode>,
  appAnchorElement
);

// If you want to start measuring performance in your app, pass a function
// to log results (for example: reportWebVitals(console.log))
// or send to an analytics endpoint. Learn more: https://bit.ly/CRA-vitals
reportWebVitals();

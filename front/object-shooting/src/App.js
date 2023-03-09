import logo from './logo.svg';

import formWizard1Image from './images/step-1.jpg';
import formWizard2Image from './images/step-2.jpg';
import formWizard3Image from './images/step-3.jpg';
import formWizard4Image from './images/step-4.jpg';
import formWizard5Image from './images/step-5.jpg';

import {ReactComponent as DoneIcon} from './images/green-checkmark.svg';
import rocketSpinner from './images/Rocket.gif';         

import './fonts/material-design-iconic-font/css/material-design-iconic-font.css';
import './css/style.css';
import './App.css';

import { createMachine, assign, actions, send } from 'xstate';
import { useMachine } from '@xstate/react';
import React from 'react';
import { useForm } from 'react-hook-form';
import { mapContext } from 'xstate/lib/utils';


const stepMachine = createMachine(
{
  id: "step",
  initial: "contact",
  states: {
    contact: {
      on: { FORWARD: "description"},
      exit: ['pushToServer']
    },
    description: {
      on: { BACK: "contact", FORWARD: "targetPlatforms" },
      exit: ['pushToServer']
    },
    targetPlatforms: {
      on: {BACK: "description", FORWARD: 'references'},
      exit: ['pushToServer']
    },
    references: {
      on: {BACK: "targetPlatforms", FORWARD: 'submiting'},
      exit: ['pushToServer']
    },
    submiting: {
      on: {BACK: "references", FORWARD: 'done'},
      entry: send('FORWARD',{delay: 1500})
    },
    done: {
      entry: ['submitToServer']
    }

  }
},
{
  actions: {
    pushToServer: (context,event) => {
      const formData = JSON.stringify(context.watch());
      const apiUrl = window.location.protocol + '//' + window.location.host + '/wp-json/gp-brief/v1/pushBrief';
      
      const requestMetadata = {
          method: 'POST',
          headers: {
              'Authorization': 'Bearer '+context.token,
              'Content-Type': 'application/json'
          },
          body: formData
      };

      fetch(apiUrl, requestMetadata)
          .then(res => res.json())
          .then(recipes => {
              console.log({ recipes });
          });
    },
    submitToServer: (context,event) => {
      const formData = JSON.stringify(context.watch());
      const apiUrl = window.location.protocol + '//' + window.location.host + '/wp-json/gp-brief/v1/submitBrief';
      
      const requestMetadata = {
          method: 'POST',
          headers: {
              'Authorization': 'Bearer '+context.token,
              'Content-Type': 'application/json'
          },
          body: formData
      };

      fetch(apiUrl, requestMetadata)
          .then(res => res.json())
          .then(recipes => {
              console.log({ recipes });
          });
    }
  }
});


const stepStatusBar = (step) => {
    let statusBar = (
      <div className="steps">
      </div>
      );
    switch ( step.value ){
      case 'contact':
        statusBar = (
          <div className="steps">
            <ul>
              <li className="first checked">
                <a id="wizard-t-0" href="#wizard-h-0" aria-controls="wizard-p-0">
                  <span className="current-info audible">current step: </span>
                  <span className="number">1.</span> 
                </a>
              </li>
               <li>
                <a id="wizard-t-0" href="#wizard-h-0" aria-controls="wizard-p-0">
                  <span className="current-info audible">current step: </span>
                  <span className="number">2.</span> 
                </a>
              </li>
              <li>
                <a id="wizard-t-0" href="#wizard-h-0" aria-controls="wizard-p-0">
                  <span className="current-info audible">current step: </span>
                  <span className="number">3.</span> 
                </a>
              </li>
              <li>
                <a id="wizard-t-0" href="#wizard-h-0" aria-controls="wizard-p-0">
                  <span className="current-info audible">current step: </span>
                  <span className="number">4.</span> 
                </a>
              </li>
            </ul>
            <p>Як з вами зв`язатися</p>
          </div>
          )
        break;
       case 'description':
        statusBar = (
          <div className="steps">
            <ul>
              <li className="first checked">
                <a id="wizard-t-0" href="#wizard-h-0" aria-controls="wizard-p-0">
                  <span className="current-info audible">current step: </span>
                  <span className="number">1.</span> 
                </a>
              </li>
               <li className="checked current">
                <a id="wizard-t-0" href="#wizard-h-0" aria-controls="wizard-p-0">
                  <span className="current-info audible">current step: </span>
                  <span className="number">2.</span> 
                </a>
              </li>
              <li>
                <a id="wizard-t-0" href="#wizard-h-0" aria-controls="wizard-p-0">
                  <span className="current-info audible">current step: </span>
                  <span className="number">3.</span> 
                </a>
              </li>
              <li>
                <a id="wizard-t-0" href="#wizard-h-0" aria-controls="wizard-p-0">
                  <span className="current-info audible">current step: </span>
                  <span className="number">3.</span> 
                </a>
              </li>
            </ul>
            <p>Опишіть товар</p>
          </div>
          )
        break;
        case 'targetPlatforms':
        statusBar = (
          <div className="steps">
            <ul>
              <li className="first checked">
                <a id="wizard-t-0" href="#wizard-h-0" aria-controls="wizard-p-0">
                  <span className="current-info audible">current step: </span>
                  <span className="number">1.</span> 
                </a>
              </li>
               <li className="checked current">
                <a id="wizard-t-0" href="#wizard-h-0" aria-controls="wizard-p-0">
                  <span className="current-info audible">current step: </span>
                  <span className="number">2.</span> 
                </a>
              </li>
              <li className="checked current">
                <a id="wizard-t-0" href="#wizard-h-0" aria-controls="wizard-p-0">
                  <span className="current-info audible">current step: </span>
                  <span className="number">3.</span> 
                </a>
              </li>
              <li>
                <a id="wizard-t-0" href="#wizard-h-0" aria-controls="wizard-p-0">
                  <span className="current-info audible">current step: </span>
                  <span className="number">3.</span> 
                </a>
              </li>
            </ul>
            <p>Для якої платформи предметна зйомка</p>
          </div>
          )
        break;
        case 'references':
        statusBar = (
          <div className="steps">
            <ul>
              <li className="first checked">
                <a id="wizard-t-0" href="#wizard-h-0" aria-controls="wizard-p-0">
                  <span className="current-info audible">current step: </span>
                  <span className="number">1.</span> 
                </a>
              </li>
               <li className="checked checked">
                <a id="wizard-t-0" href="#wizard-h-0" aria-controls="wizard-p-0">
                  <span className="current-info audible">current step: </span>
                  <span className="number">2.</span> 
                </a>
              </li>
              <li className="checked checked">
                <a id="wizard-t-0" href="#wizard-h-0" aria-controls="wizard-p-0">
                  <span className="current-info audible">current step: </span>
                  <span className="number">3.</span> 
                </a>
              </li>
              <li className="checked current">
                <a id="wizard-t-0" href="#wizard-h-0" aria-controls="wizard-p-0">
                  <span className="current-info audible">current step: </span>
                  <span className="number">3.</span> 
                </a>
              </li>
            </ul>
            <p>Референси</p>
          </div>
          )
        break;    
    }
    return statusBar;
}


const formContent = (step,register,errors) => {
    let content = (
      <div>
        <div className="form-row">
        </div>
      </div>
      );
    switch ( step.value ){
      case 'contact':
        content = (
          <div>
            <section id="contact">
              <div className="form-row">
                <div className="form-holder">
                  <input 
                    type="text" 
                    placeholder="Ім`я" 
                    className="form-control"
                    name="name"
                    {...register('name', { required: {value: true, message: 'Пожалуйста введите имя'} })}
                  />
                  {errors.name && (
                    <span role="alert" className="validation-error">
                      {errors.name.message}
                    </span>
                  )}
                </div>
                <div className="form-holder">
                  <input 
                    type="text" 
                    placeholder="Email" 
                    className="form-control"
                    name="email"
                    {...register('email', { required: {value: true, message: 'Пожалуйста введите email'} })}
                    />
                    {errors.email && (
                      <span role="alert" className="validation-error">
                        {errors.email.message}
                      </span>
                    )}
                </div>
              </div>
              <div className="form-row">
                <div className="form-holder">
                  <input 
                    type="text"
                    key="mobile" 
                    placeholder="Мобільний" 
                    className="form-control"
                    name="mobile"
                    {...register('mobile', { required: {value: true, message: 'Пожалуйста введите телефон'} })}
                  />
                  {errors.mobile && (
                    <span role="alert" className="validation-error">
                      {errors.mobile.message}
                    </span>
                  )}
                </div>
                <div className="form-holder">
                  <input 
                    type="text" 
                    placeholder="Instagram" 
                    className="form-control"
                    name="instagram"
                    key="instagram" 
                    {...register('instagram')}
                  />
                </div>
              </div>
               <div className="form-row">
                <div className="form-holder" style={{alignSelf: "flex-end"}, {transform: "translateY(4px)"}}>
                  <div className="checkbox-tick">
                    <label className="telegram">
                      <input 
                        type="radio" 
                        name="messenger" 
                        value="telegram" 
                        defaultChecked={true}
                        {...register('messenger')}
                      />Telegram
                      <br/>
                      <span className="checkmark"></span>
                    </label>
                    <label className="viber">
                      <input 
                        type="radio" 
                        name="messenger" 
                        value="viber"
                        {...register('messenger')}
                      />Viber
                      <br/>
                      <span className="checkmark"></span>
                    </label>
                    <label className="instagram">
                      <input 
                        type="radio" 
                        name="messenger" 
                        value="instagram"
                        {...register('messenger')}
                      />Instagram
                      <br/>
                      <span className="checkmark"></span>
                    </label>
                  </div>
                </div>
                <div className="form-holder">
                  <div className="checkbox-circle">
                    <label>
                      <input 
                        type="checkbox"
                        {...register('dontCall')}
                      /> Не дзвоніть. Будемо спілкуватися текстом.
                      <span className="checkmark"></span>
                    </label>
                  </div>
                </div>
              </div>
            </section>
          </div>

          );
        break;
      case 'description':
         content = (
          <div>
            <section id='description'>
              <div className="form-row">
                <div className="form-holder w-100">
                  <textarea 
                    name="itemsDescription" 
                    id="" 
                    placeholder="Опишіть товар" 
                    className="form-control" 
                    style={{height: "99px"}}
                    {...register('itemsDescription', { required: true })}
                  />
                </div>
              </div>
              <div className="form-row">
                <div className="form-holder">
                  <input 
                    type="text"
                    name="itemsCount" 
                    id='itemsCount'
                    placeholder="Кількість товарів" 
                    className="form-control"
                    {...register('itemsCount', { required: {value: true, message: 'Приблизна кількість товарів.'} })}
                    />
                    {errors.itemsCount && (
                      <span role="alert" className="validation-error">
                        {errors.itemsCount.message}
                      </span>
                    )}
                </div>
                <div className="form-holder">
                  <input 
                    type="text" 
                    placeholder="Кількість ракурсів" 
                    className="form-control"
                    {...register('foreshortening', { required: {value: true, message: 'Скільки ракурсів будемо знімати?'} })}
                  />
                  {errors.foreshortening && (
                    <span role="alert" className="validation-error">
                      {errors.foreshortening.message}
                    </span>
                  )}
                </div>
              </div>
            </section>
          </div>
          );
        break;
      case 'targetPlatforms':
        content = (
          <div>
            <div className="form-row">
              <div className="checkbox-circle-multi">
                <label>
                  <input 
                    type="checkbox" 
                    value="olx"
                    {...register('targetPlatform')}
                    /> OLX
                  <span className="checkmark"></span>
                </label>
                <label>
                  <input 
                    type="checkbox" 
                    value="prom"
                    {...register('targetPlatform')}
                  /> prom
                  <span className="checkmark"></span>
                </label>
                <label>
                  <input 
                    type="checkbox" 
                    value="amazon"
                    {...register('targetPlatform')}
                  /> amazon
                  <span className="checkmark"></span>
                </label>
                <label>
                  <input 
                    type="checkbox" 
                    value="Instagram"
                    {...register('targetPlatform')}
                  /> Instagram
                  <span className="checkmark"></span>
                </label>
                <label>
                  <input 
                    type="checkbox" 
                    value="website"
                    {...register('targetPlatform')}
                  />website
                  <span className="checkmark"></span>
                </label>
              </div>
            </div>
            <div className="form-row">
              <div className="form-holder w-100">
                <textarea 
                  name="" 
                  id="" 
                  placeholder="Інша платформа або додаткові коментарі?" 
                  className="form-control" 
                  style={{height: "99px"}}
                  {...register('platformDetails')}
                />
              </div>
            </div>
          </div>
          );
        break;
      case 'references':
         content = (
          <div>
            <div className="form-row">
              <div className="form-holder w-100">
                <textarea 
                name="" 
                id="" 
                placeholder="Посилання на зйомки, що подобаются" 
                className="form-control" 
                style={{height: "99px"}}
                {...register('references')}
              />
              </div>
            </div>
            <div className="form-holder">
                <div className="checkbox-circle">
                  <label>
                    <input 
                      type="checkbox"
                      {...register('urgentShooting')}
                    /> Необхідна термінова зйомка, швидше ніж за 7-10 днів.
                    <span className="checkmark"></span>
                  </label>
                </div>
              </div>
          </div>
          );
        break;
      case 'submiting':
         content = (
          <div>
            <div className="form-holder">
              <div className="congratulation-msg">  
                <img src={rocketSpinner}/>
                <p>Відправка форми …</p>
              </div>
            </div>
          </div>
        );
        break; 
      case 'done':
         content = (
          <div>
            <div className="form-holder">
              <div className="congratulation-msg">  
                <p>
                  <DoneIcon/>
                  <h4>Чудово! Бриф надіслано!</h4>
                  <br/>
                  { step.context.watch('name') + ', ми' && 'Ми' } опрацюємо бриф і зв`яжемось з вами як найшвидше.
                </p>
              </div>
            </div>
          </div>
        );
        break; 
      }
    return content;
    }


const buttons = (step,send,watch,token) => {
  let controlButtons = (
    <div className="form-row">
    </div>
  );
  switch ( step.value ){
    case 'contact':
      controlButtons = (
        
          <div className="actions clearfix">
            <ul role="menu" aria-label="Pagination">
              <li aria-hidden="false" aria-disabled="false">
                <a onClick={ () => { 
                  step.context.watch = watch;
                  step.context.token = token; 
                  send("FORWARD"); 
                } } role="menuitem" className="next-step">Далі</a>
              </li>
            </ul>
          </div>
     
        );
      break;
    case 'description':
      controlButtons = (
       
          <div className="actions clearfix">
            <ul role="menu" aria-label="Pagination">
              <li aria-disabled="false">
                <a onClick={ () => {
                  step.context.watch = watch;
                  step.context.token = token; 
                  send("BACK"); 
                } } role="menuitem" className="previous-step">Назад</a>
              </li>
              <li aria-hidden="false" aria-disabled="false">
                <a onClick={ () => {
                  step.context.watch = watch;
                  step.context.token = token; 
                  send("FORWARD");
                } } role="menuitem" className="next-step">Далі</a>
              </li>
            </ul>
          </div>
       
        );
      break;
    case 'targetPlatforms':
    controlButtons = (
      
        <div className="actions clearfix">
          <ul role="menu" aria-label="Pagination">
            <li aria-hidden="false" aria-disabled="false">
              <a onClick={ () => {
                step.context.watch = watch;
                step.context.token = token; 
                send("BACK");
               } } role="menuitem" className="previous-step">Назад</a>
            </li>
            <li aria-hidden="false" aria-disabled="false">
              <a onClick={ () => {
                step.context.watch = watch;
                step.context.token = token; 
                send("FORWARD");
              } } role="menuitem" className="next-step">Далі</a>
            </li>
          </ul>
        </div>
     
      );
      break;
      case 'references':
      controlButtons = (
      
        <div className="actions clearfix">
          <ul role="menu" aria-label="Pagination">
            <li aria-hidden="false" aria-disabled="false">
              <a onClick={ () => {
                step.context.watch = watch;
                step.context.token = token; 
                send("BACK"); 
              } } role="menuitem" className="previous-step">Назад</a>
            </li>
            <li aria-hidden="false" aria-disabled="false">
              <a onClick={ () => send("FORWARD") } role="menuitem" className="submit-step">Надіслати</a>
            </li>
          </ul>
        </div>
     
      );
      break;
    }
  return controlButtons;
}
const formImage = (step) => {
  switch ( step.value ){
    case 'contact':
      return formWizard1Image;
    case 'description':
      return formWizard2Image;
    case 'targetPlatforms':
      return formWizard3Image;
    case 'references':
      return formWizard4Image;
    case 'submiting':
      return formWizard5Image;
  }
  return formWizard5Image;
}
function App( token ) {
  const [currentStep, send] = useMachine(stepMachine);
  const active = currentStep.matches("contact");
  const { watch, register, formState: {errors} } = useForm({mode:'all'});
  console.log( token );
  return (
    <div className="App">
      <div className="wrapper">
        <form action="" id="wizard" className="wizard clearfix" role="application">
            <h2></h2>
            <section>
              <div className="inner">
                <div className="image-holder">
                  <img src={formImage(currentStep)} alt=""/>
                </div>
                <div className="form-content" >
                  <div className="form-header">
                    <h3>{'Бриф. Предметна зйомка'}</h3>
                  </div>

                  { stepStatusBar(currentStep) }
                  { formContent(currentStep,register,errors) }    
                  { buttons(currentStep, send, watch, token.token) }

                </div>
              </div>
            </section>
        </form>
      </div>
    </div>
  );
}

export default App;

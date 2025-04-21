import Echo from 'laravel-echo'
import Pusher from 'pusher-js'

window.Pusher = Pusher

const echo = new Echo({
  broadcaster: 'pusher',
  key: 'c1c7ff3f6141d637ab84',
  cluster: 'ap1',
  forceTLS: true
})

export default echo

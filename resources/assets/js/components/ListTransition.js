
import Velocity from 'velocity-animate'

export default {
    
    methods: {

            /**
             * customizing the transition states
             */

            beforeEnter: function (el) {
                el.style.opacity = 0
                el.style.height = 0
            },

            enter: function (el, done) {
                var delay = el.dataset.index * 25
                var dur = 200 - (el.dataset.index * 10);
                if (dur < 25) {
                    dur = 25;
                }
                setTimeout(function () {
                Velocity(
                    el,
                        { opacity: 1, height: '30px' },
                        { complete: done, duration: dur }
                    )
                }, delay)
            },

            leave: function (el, done) {
                var delay = el.dataset.index * 25
                var dur = 200 - (el.dataset.index * 10);
                if (dur < 25) {
                    dur = 25;
                }
                setTimeout(function () {
                Velocity(
                    el,
                        { opacity: 0, height: 0 },
                        { complete: done, duration: dur }
                    )
                }, delay)
            }


    }

}

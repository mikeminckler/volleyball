<template>

    <div class="timepicker">
        <input 
            ref="input"
            :name="name"
            :id="name"
            class="text-input timepicker"
            placeholder="HH:MM"
            :value="value"
            @input="updateValue($event.target.value)"
        ></input>
    </div>

</template>

<script>

    export default {

        props: ['name', 'value'],

        mounted() {
            
            $(this.$refs.input).datetimepicker({
                dateFormat: 'yy-mm-dd',
                showAnim: 'slideDown',
                stepMinute: 5,
                changeMonth: true,
                changeYear: true,
                onSelect: (dateText, inst) => {
                    this.updateValue(dateText);
                },
                onClose: (dateText, inst) => {
                    this.updateValue(dateText);
                },
                beforeShow: function (input, object) {
                    setTimeout(function(object) {
                        $('div.ui_tpicker_hour_slider').find('span.ui-slider-handle').attr('id', 'handle_hour');
                        $('div.ui_tpicker_minute_slider').find('span.ui-slider-handle').attr('id', 'handle_minute');
                    }, 100);
                }
            });
        },

        methods: {
            updateValue: function (value) {
                this.$emit('input', value);
            }
        }

    }

</script>

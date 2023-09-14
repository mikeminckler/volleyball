import dayjs from 'dayjs';
import utc from 'dayjs/plugin/utc';
import timezone from 'dayjs/plugin/timezone';
import advancedFormat from 'dayjs/plugin/advancedFormat'
import relativeTime from 'dayjs/plugin/relativeTime'
import customParseFormat from 'dayjs/plugin/customParseFormat'

dayjs.extend(utc);
dayjs.extend(timezone);
dayjs.extend(advancedFormat);
dayjs.extend(relativeTime);
dayjs.extend(customParseFormat);

export function useDates() {

    function displayDateTime(date) {
        return dayjs(date).format('YYYY-MM-DD H:mm z');
    }

    function displayHumanDateTime(date) {
        return dayjs(date).format('dddd MMMM D, YYYY h:mm a z');
    }

    function displayShortDateTime(date) {
        return dayjs(date).format('MMM D, \'YY h:mma');
    }

    return {
        displayDateTime,
        displayHumanDateTime,
        displayShortDateTime,
    }
}

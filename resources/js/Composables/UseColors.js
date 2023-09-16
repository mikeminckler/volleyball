export function useColors() {

    function getColor(score) {
        if (score === 1) {
            return 'score-green';
        } else if (score === -1) {
            return 'score-red';
        } else if (score > 0) {
            return 'score-blue';
        } else if (score < 0) {
            return 'score-yellow';
        } else {
            return 'score-gray';
        }
    }

    return {
        getColor,
    }
}

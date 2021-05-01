/**
   * Limit a number to a maximum and minumum value
   * 
   * @param {number} n the number to limit
   * @param {number} max the max it can be
   * @param {number} min the min it can be
   * @returns {number}
   */
function limit(n, max, min) {
    return (n < min) ? min :
        (n > max) ? max :
            n;
}

export {limit};
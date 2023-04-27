import { Controller } from '@hotwired/stimulus';

/*
 *
 */
export default class extends Controller {

    static targets = ['duree', 'tuteur']

    static values = {
        updateUrl: String,
        enfant: String,
        heure: String,
        date: String,
    }

    up() {
        let currentValue = parseInt(this.dureeTarget.value);
        if (isNaN(currentValue)) {
            currentValue = 0;
        }
        this.dureeTarget.value = currentValue + 1;
    }

    down() {
        let currentValue = parseFloat(this.dureeTarget.value);
        if (isNaN(currentValue)) {
            currentValue = 0;
        }
        if (currentValue === 0) {
            this.dureeTarget.value = 0;
        } else {
            this.dureeTarget.value = currentValue - 1;
        }
    }

    async updateSync() {

        console.log(this.updateUrlValue);
        const response = await fetch(`${this.updateUrlValue}`, {
            method: 'POST',
            body: JSON.stringify({
                'enfantid': this.enfantValue,
                'tuteurid': this.tuteurTarget.value,
                'heure': this.heureValue,
                'date': this.dateValue,
                'duree': this.dureeTarget.value
            }),
            headers: {
                'Content-type': 'application/json; charset=UTF-8'
            }
        });

        let responseString = await response.text();
        console.log(responseString);

        var data = JSON.parse(responseString);
        if (data.error) {

        } else {

        }
    }
}

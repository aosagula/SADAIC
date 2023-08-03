export default class CustomStorage {
    constructor(base, initial = {}) {
        this.base = base;

        localStorage.setItem(
            this.base,
            JSON.stringify(initial)
        );
    }

    get() {
        return JSON.parse(localStorage.getItem(this.base));
    }

    getField(field) {
        const storage = JSON.parse(localStorage.getItem(this.base));

        const pattern = /\[\s*(\w+)\s*\]/g;
        if (field.match(pattern)) {
            const subField = pattern.exec(field)[1];
            field = field.substring(0, field.indexOf('['));

            if (!storage.hasOwnProperty(field)) {
                return null;
            }

            if (!storage[field].hasOwnProperty(subField)) {
                return null;
            }

            return storage[field][subField];
        } else {
            if (!storage.hasOwnProperty(field)) {
                return null;
            }

            return storage[field];
        }
    }

    setField(field, value) {
        const storage = JSON.parse(localStorage.getItem(this.base));
        const pattern = /\[\s*(\w+)\s*\]/g;

        if (field.match(pattern)) {
            let subField = pattern.exec(field)[1];
            field = field.substring(0, field.indexOf('['));

            if (!storage.hasOwnProperty(field)) {
                storage[field] = {};
            }

            storage[field][subField] = value;
        } else {
            storage[field] = value;
        }

        localStorage.setItem(this.base, JSON.stringify(storage));
    }

    removeField(field) {
        const storage = JSON.parse(localStorage.getItem(this.base));
        delete storage[field];

        localStorage.setItem(this.base, JSON.stringify(storage));
    }
}

export class PeopleStorage extends CustomStorage {
    constructor(base, initial = []) {
        super(base);

        this.setField('people', initial);
    }

    addPerson(person) {
        const storage = this.getField('people');
        storage.push(person);
        this.setField('people', storage);
    }

    getPerson(doc, member) {
        const storage = this.getField('people');

        const idx = this.findPerson(doc, member);
        if (idx == -1) {
            return null;
        }

        return storage[idx];
    }

    updatePerson(doc, member, person) {
        const storage = this.getField('people');

        const idx = this.findPerson(doc, member);
        if (idx == -1) {
            storage.push(person);
        } else {
            storage[idx] = person;
        }

        this.setField('people', storage);
    }

    removePerson(doc, member) {
        const storage = this.getField('people');

        const idx = this.findPerson(doc, member);
        if (idx == -1) {
            return;
        }

        storage.splice(idx, 1);

        this.setField('people', storage);
    }

    getPersonField(doc, member, field) {
        const person = this.getPerson(doc, member);
        return person[field];
    }

    updatePersonField(doc, member, ...params) {
        let person = this.getPerson(doc, member);

        if (typeof params[0] === 'object') {
            for (const field in params[0]) {
                person[field] = params[0][field];
            }
        } else {
            person[params[0]] = params[1];
        }

        this.updatePerson(doc, member, person);
    }

    findPerson(doc_number, member_id) {
        const storage = this.getField('people');

        let idx = -1;
        if (doc_number != '' && member_id != '') {
            idx = storage.findIndex(e => e.doc_number == doc_number && e.member_id == member_id);
        } else if(doc_number != '') {
            idx = storage.findIndex(e => e.doc_number == doc_number);
        } else {
            idx = storage.findIndex(e => e.member_id == member_id);
        }

        return idx;
    }
}
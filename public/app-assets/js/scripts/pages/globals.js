let obj = {
    _isLoaded: false,
    get value() {
        alert("This is working too from getter")
        console.log('Value accessed:', this._isLoaded);
        return this._isLoaded;
    },
    set value(newValue) {
        // if (newValue !== this._isLoaded)
        // {
        //     alert("Value is chened")
        // }
        // console.log('Value changed from', this._isLoaded, 'to', newValue);
        // this._isLoaded = newValue;
        // alert("This is working and event is being fired")
        document.dispatchEvent(new Event('valueChanged'));
    }
};
alert("Gloabasl are loaded")

export default obj

//

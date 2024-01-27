

export default class BillType extends React.Component {
    constructor(props) {
        super(props);
        this.state = { 
            jsx: this.determineTypeJSX(this.props.type)
        };
    }

    determineTypeJSX(type) {
        var billType = "";
        type = type.toUpperCase();
        switch (type) {
            case "S":
            case "HR": billType = "Bill"; break;
            case "HRES": billType = "Resolution"; break;
            case "SCONRES": billType = "Concurrent Resolution"; break;
        }

        return (<div className={'bill-type-text'}>{billType}</div>);
    }

    render() {
        return(
            <div className={'bill-type-container'}>
                {this.state.jsx}
            </div>
        );
    }
}
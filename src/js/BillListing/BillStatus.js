import APICallingComponent from "../Api/APICallingComponent.js";
import DateUtil from "../Util/DateUtil.js";

export default class BillStatus extends APICallingComponent{
    static possibleStatuses = {
        'Introduced': ['introduced'], 
        'Referral': ['referred to'],
        'Passed House': ['pass', 'house'], 
        'In Senate': ['received in the senate'],
        'Passed Senate': ['pass', 'senate'], 
        'To President': ['to president'], 
        'Became Law': ['became public law', 'signed by president']
    };
    static determineStatusFromActionText(actionText) {
        var words = actionText.replace(/[^A-Za-z]/g, ' ');
        var words = words.split(/[\s]+/);
    }

    constructor(props) {
        super(props);
        this.state = { 
            jsx: [],
            updated: DateUtil.buildSimpleDateString(this.props.action.actionDate)
        };
        
        //this.state.jsx = this.generateStatus(this.props.latestAction);
    }

    generateStatus(latestAction) {
        var statuses = BillStatus.possibleStatuses;
        var currentStatus = latestAction.text.toLowerCase();
        BillStatus.determineStatusFromActionText(currentStatus)

        var jsx = [];
        for (var statusIndex in statuses) {
            var status = statuses[statusIndex];
            var active = false;
            if (currentStatus.includes(status.toLowerCase())) active = true;
            jsx.push(<span key={statusIndex} className={'bill-status-item ' + (active?'active':'inactive')}>{status}</span>);
        }
        return jsx;
    }

    render() {
        return(
            <div className={'bill-status-container full-width'}>
                <div className={'bill-status'}>
                    <div className={'bill-status-time'}>Last Action: {this.state.updated}</div>
                    <div className={'bill-status-separator'}> - </div>
                    <div className={'bill-status-text'}>{this.props.action.text}</div>
                </div>
            </div>
        );
    }
}
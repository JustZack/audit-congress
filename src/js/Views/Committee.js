import APICallingComponent from "../Api/APICallingComponent"
import UrlUtil from "../Util/UrlUtil.js";

export default class Committee extends APICallingComponent {
    constructor(props) {
        super(props);
        this.state = { 
          isset: false,
          jsx: (<h1>Loading Data...</h1>),
          committee: null
        };
    
        this.handleCommitteeData = this.handleCommitteeData.bind(this);
        this.handleBackClick = this.handleBackClick.bind(this);
        this.componentDidMount = this.componentDidMount.bind(this);
        this.getJSX = this.getJSX.bind(this);
      }
      
      static getPath(chamber, id) {
        return `committee/${chamber}/${id}`;
      }
    
      componentDidMount = () => {
        var comm = this.props.committee;
        comm.type = `${comm.type}`.toLowerCase();
        
        var name = comm.name;
        var chamber = comm.chamber;
        var id = comm.systemCode;
        var path = Committee.getPath(chamber, id);
        UrlUtil.setWindowUrl(name, path);
    
        //this.APIFetch("fullBill", {congress: bill.congress, type: bill.type, number: bill.number}, this.handleBillData);
      };
    
      handleCommitteeData = (json) => {
        var committeeObj = json.committee;
        this.setState({
            committee: committeeObj,
            jsx: this.getJSX(committeeObj),
            isset: true
        });
      }
    
      getJSX = (bill) => {
        return (
          <div>

          </div>
        )
      }
      
      handleBackClick = () => {
        this.props.setView("bill-listing");
      }
      
      render() {
        console.log(this.state.bill);
        return (
          <div className="detailed-view">
            <button onClick={this.handleBackClick}>Back To Listing</button>   
            {this.state.jsx}      
          </div>
        );
      }
}
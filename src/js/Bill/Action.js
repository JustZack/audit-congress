import DateUtil from "../Util/DateUtil";

export default class Action extends React.Component {
    constructor(props) {
      super(props);
      this.state = { 
        isset: false,
        jsx: null
      };
    }
    
    componentDidMount = () => {
        var a = this.props.action;

        var jsx = (
            <div>
                [{DateUtil.buildSimpleDateString(a.actionDate)}] (<i>{a.sourceSystem.name}</i>) {a.text}
            </div>
        )
                
        this.setState({
          jsx: jsx
        });
    };
    
    render() {
      return (
      <div className="">
          {this.state.jsx}
      </div>
      );
    }
  }
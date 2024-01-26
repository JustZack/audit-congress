import Committee from "./Committee.js";

export default class Committees extends React.Component {
    constructor(props) {
      super(props);
      this.state = { 
        isset: false,
        jsx: null
      };
    }
    
    componentDidMount = () => {
        var jsx = []
        var comms = this.props.committees;
        if (comms !== undefined) {
          for (var i = 0;i < comms.length;i++) {
              var c = comms[i];
              var key = `${c.chamber}-${c.systemCode}-${i}`;
              jsx.push(<Committee committee={c} key={key} setView={this.props.setView}/>);
          }
          this.setState({
            jsx: jsx
          });
        }
    };
    
    render() {
      return (
      <div className="">
            <h3>Committees</h3>
            <div className="">
                {this.state.jsx}
            </div>
      </div>
      );
    }
  }
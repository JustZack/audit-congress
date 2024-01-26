import Action from "./Action.js";

export default class Actions extends React.Component {
    constructor(props) {
      super(props);
      this.state = { 
        isset: false,
        jsx: null
      };
    }
    
    componentDidMount = () => {
        var jsx = []
        var actions = this.props.actions;
        if (actions !== undefined) {
          for (var i = 0;i < actions.length;i++) {
              var a = actions[i];
              var key = `${a.actionCode}-${a.actionDate}-${i}`;
              jsx.push(<Action action={a} key={key}/>);
          }
          this.setState({
            jsx: jsx
          });
        }
    };
    
    render() {
      return (
      <div className="">
            <h3>Actions</h3>
            <div className="">
                {this.state.jsx}
            </div>
      </div>
      );
    }
  }
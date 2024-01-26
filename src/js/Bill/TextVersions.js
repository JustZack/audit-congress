import TextVersion from "./TextVersion.js";

export default class TextVersions extends React.Component {
    constructor(props) {
      super(props);
      this.state = { 
        isset: false,
        jsx: null
      };
    }
    
    componentDidMount = () => {
        var jsx = []
        var textVersions = this.props.textVersions;
        if (textVersions !== undefined) {
          for (var i = 0;i < textVersions.length;i++) {
              var tv = textVersions[i];
              jsx.push(<TextVersion textVersion={tv} key={i}/>);
          }
          this.setState({
            jsx: jsx
          });
        }
    };
    
    render() {
      return (
      <div className="">
            <h3>Text Versions</h3>
            <div className="">
                {this.state.jsx}
            </div>
      </div>
      );
    }
  }
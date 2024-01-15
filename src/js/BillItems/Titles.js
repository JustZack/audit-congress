import Title from "./Title.js";

export default class Titles extends React.Component {
    constructor(props) {
      super(props);
      this.state = { 
        isset: false,
        jsx: null
      };
    }
    
    componentDidMount = () => {
        var jsx = []
        var titles = this.props.titles;
        for (var i = 0;i < titles.length;i++) {
            var t = titles[i];
            jsx.push(<Title title={t} key={i}/>);
        }
        this.setState({
          jsx: jsx
        });
    };
    
    render() {
      return (
      <div className="">
            <h3>Title History</h3>
            <div className="">
                {this.state.jsx}
            </div>
      </div>
      );
    }
  }
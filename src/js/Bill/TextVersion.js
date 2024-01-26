import DateUtil from "../Util/DateUtil.js";

export default class TextVersions extends React.Component {
    constructor(props) {
      super(props);
      this.state = { 
        isset: false,
        jsx: null
      };
    }
    
    componentDidMount = () => {
        var tv = this.props.textVersion;
        if (tv !== undefined) {
          var links = [];
          for (var i = 0;i < tv.formats.length;i++) {
            var form = tv.formats[i];
            var separator = i < tv.formats.length-1 ? ", " : "";
            links.push(<span key={i}><a target="_blank" href={form.url}>[{form.type}]</a>{separator}</span> );
          }

          var jsx = (
              <div>
                  [{DateUtil.buildSimpleDateString(tv.date)}] {tv.type}<br/>
                  {links}
              </div>
          )
                  
          this.setState({
            jsx: jsx
          });
        }
    };
    
    render() {
      return (
      <div className="">
          {this.state.jsx}
      </div>
      );
    }
  }
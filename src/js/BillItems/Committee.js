export default class Committee extends React.Component {
    constructor(props) {
      super(props);
      this.state = { 
        isset: false,
        jsx: null
      };
    }
    
    componentDidMount = () => {
        var c = this.props.committee;

        var jsx = (
            <div>
                {c.name}
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
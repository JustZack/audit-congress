export default class Committee extends React.Component {
    constructor(props) {
      super(props);
      this.state = { 
        isset: false,
        jsx: null
      };

      this.handleCommitteeInfoClick = this.handleCommitteeInfoClick.bind(this);
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
    
    handleCommitteeInfoClick = () => {
      this.props.setView("committee", this.props.committee);
    }

    render() {
      return (
      <strong onClick={this.handleCommitteeInfoClick} className={'link'}>
          {this.state.jsx}
      </strong>
      );
    }
  }
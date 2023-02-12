import React from 'react';
import createClass from 'create-react-class';
import PropTypes from 'prop-types';
import Select from 'react-select';
import fetch from 'isomorphic-fetch';
import AsyncSelect from "react-select/async";

const SelectPost = createClass({
    displayName: 'SelectPost',
    getInitialState () {
        return {
            backspaceRemoves: true,
            multi: false,
            creatable: false,
            value: this.props.initial_value
        };
    },
    onChange (value) {
        this.setState({
            value: value,
        });
        this.props.onChange(value);
    },

    getData (input) {
        if (!input) {
            var url = this.props.restUrl;

            return fetch( url, {
                credentials: 'same-origin',
                method: 'get',
                headers: {
                }})
                .then( this.handleFetchErrors )
                .then( ( response ) => response.json() )
                .then( ( json ) => {
                    var dataOptions = json.map( function(opt, i){
                        return {value: opt.id, label: opt.title.rendered}
                    });
                    console.log(dataOptions);
                    return { options: dataOptions };
                }).catch(function(e) {
                    console.log("error");
                    console.log(e)
                });
        }
        var sanatizedInput = this.sanatizeInput( input );
        var url = this.props.restUrl + sanatizedInput;

        return fetch( url, {
            credentials: 'same-origin',
            method: 'get',
            headers: {
            }})
            .then( this.handleFetchErrors )
            .then( ( response ) => response.json() )
            .then( ( json ) => {
                var dataOptions = json.map( function(opt, i){
                    return {value: opt.id, label: opt.title.rendered}
                });
                console.log(dataOptions);
                return { options: dataOptions };
            }).catch(function(e) {
                console.log("error");
                console.log(e)
            });
    },
    handleFetchErrors(response) {
        if (!response.ok) {
            console.log('fetch error, status: ' + response.statusText);
        }
        return response;
    },
    sanatizeInput( input ){
        var output = input
            .replace(/[^\w\s\d]/gi, '')
            .replace(/[`~!@#$%^&*()_|+\-=?;:'",.<>\{\}\[\]\\\/]/gi, '')
            .split(' ')
            .join('-');
        if( output == "" ){
            output = "null";
        }
        return output;
    },
    toggleBackspaceRemoves () {
        this.setState({
            backspaceRemoves: !this.state.backspaceRemoves
        });
    },
    toggleCreatable () {
        this.setState({
            creatable: !this.state.creatable
        });
    },
    render () {
        return (
            <div className="section">
                <AsyncSelect
					defaultOptions
                    value={this.state.value}
                    onInputChange={this.onChange}
                    loadOptions={this.getData}
                />
            </div>
        );
    }
});

export default SelectPost;

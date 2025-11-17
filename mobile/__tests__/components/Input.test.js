import React from 'react';
import { render, fireEvent } from '@testing-library/react-native';
import Input from '../../src/components/common/Input';

describe('Input Component', () => {
  it('should render correctly with placeholder', () => {
    const { getByPlaceholderText } = render(
      <Input placeholder="Enter text" onChangeText={() => {}} />
    );
    expect(getByPlaceholderText('Enter text')).toBeTruthy();
  });

  it('should render with label', () => {
    const { getByText } = render(
      <Input label="Username" placeholder="Enter username" onChangeText={() => {}} />
    );
    expect(getByText('Username')).toBeTruthy();
  });

  it('should call onChangeText when text changes', () => {
    const onChangeText = jest.fn();
    const { getByPlaceholderText } = render(
      <Input placeholder="Enter text" onChangeText={onChangeText} />
    );

    fireEvent.changeText(getByPlaceholderText('Enter text'), 'test value');
    expect(onChangeText).toHaveBeenCalledWith('test value');
  });

  it('should display current value', () => {
    const { getByDisplayValue } = render(
      <Input value="Current Value" placeholder="Enter text" onChangeText={() => {}} />
    );
    expect(getByDisplayValue('Current Value')).toBeTruthy();
  });

  it('should show error message when error prop is provided', () => {
    const { getByText } = render(
      <Input
        placeholder="Enter text"
        onChangeText={() => {}}
        error="This field is required"
      />
    );
    expect(getByText('This field is required')).toBeTruthy();
  });

  it('should not be editable when disabled', () => {
    const { getByPlaceholderText } = render(
      <Input placeholder="Enter text" onChangeText={() => {}} disabled />
    );

    const input = getByPlaceholderText('Enter text');
    expect(input.props.editable).toBe(false);
  });

  it('should apply secureTextEntry for password inputs', () => {
    const { getByPlaceholderText } = render(
      <Input placeholder="Password" onChangeText={() => {}} secureTextEntry />
    );

    const input = getByPlaceholderText('Password');
    expect(input.props.secureTextEntry).toBe(true);
  });

  it('should support multiline input', () => {
    const { getByPlaceholderText } = render(
      <Input placeholder="Description" onChangeText={() => {}} multiline numberOfLines={4} />
    );

    const input = getByPlaceholderText('Description');
    expect(input.props.multiline).toBe(true);
    expect(input.props.numberOfLines).toBe(4);
  });

  it('should apply different keyboard types', () => {
    const keyboardTypes = ['default', 'email-address', 'numeric', 'phone-pad'];

    keyboardTypes.forEach((type) => {
      const { getByPlaceholderText } = render(
        <Input placeholder="Test" onChangeText={() => {}} keyboardType={type} />
      );

      const input = getByPlaceholderText('Test');
      expect(input.props.keyboardType).toBe(type);
    });
  });

  it('should apply custom styles', () => {
    const customStyle = { marginBottom: 20 };
    const customInputStyle = { fontSize: 18 };

    const { getByPlaceholderText } = render(
      <Input
        placeholder="Test"
        onChangeText={() => {}}
        style={customStyle}
        inputStyle={customInputStyle}
      />
    );

    const input = getByPlaceholderText('Test');
    expect(input.props.style).toContainEqual(customInputStyle);
  });
});

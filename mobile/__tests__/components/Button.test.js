import React from 'react';
import { render, fireEvent } from '@testing-library/react-native';
import Button from '../../src/components/common/Button';

describe('Button Component', () => {
  it('should render correctly with title', () => {
    const { getByText } = render(<Button title="Click Me" onPress={() => {}} />);
    expect(getByText('Click Me')).toBeTruthy();
  });

  it('should call onPress when pressed', () => {
    const onPress = jest.fn();
    const { getByText } = render(<Button title="Click Me" onPress={onPress} />);

    fireEvent.press(getByText('Click Me'));
    expect(onPress).toHaveBeenCalledTimes(1);
  });

  it('should not call onPress when disabled', () => {
    const onPress = jest.fn();
    const { getByText } = render(<Button title="Click Me" onPress={onPress} disabled />);

    fireEvent.press(getByText('Click Me'));
    expect(onPress).not.toHaveBeenCalled();
  });

  it('should show loading indicator when loading', () => {
    const { queryByText, UNSAFE_getByType } = render(
      <Button title="Click Me" onPress={() => {}} loading />
    );

    expect(queryByText('Click Me')).toBeNull();
    expect(UNSAFE_getByType('ActivityIndicator')).toBeTruthy();
  });

  it('should not call onPress when loading', () => {
    const onPress = jest.fn();
    const { UNSAFE_getByType } = render(
      <Button title="Click Me" onPress={onPress} loading />
    );

    fireEvent.press(UNSAFE_getByType('TouchableOpacity'));
    expect(onPress).not.toHaveBeenCalled();
  });

  it('should render with different variants', () => {
    const variants = ['primary', 'secondary', 'outline', 'ghost', 'danger'];

    variants.forEach((variant) => {
      const { getByText } = render(
        <Button title="Test" onPress={() => {}} variant={variant} />
      );
      expect(getByText('Test')).toBeTruthy();
    });
  });

  it('should render with different sizes', () => {
    const sizes = ['sm', 'md', 'lg'];

    sizes.forEach((size) => {
      const { getByText } = render(
        <Button title="Test" onPress={() => {}} size={size} />
      );
      expect(getByText('Test')).toBeTruthy();
    });
  });

  it('should apply fullWidth style when fullWidth prop is true', () => {
    const { getByText } = render(
      <Button title="Click Me" onPress={() => {}} fullWidth />
    );
    const button = getByText('Click Me').parent;
    expect(button.props.style).toContainEqual({ width: '100%' });
  });

  it('should apply custom styles', () => {
    const customStyle = { marginTop: 20 };
    const customTextStyle = { fontStyle: 'italic' };

    const { getByText } = render(
      <Button
        title="Click Me"
        onPress={() => {}}
        style={customStyle}
        textStyle={customTextStyle}
      />
    );

    expect(getByText('Click Me').parent.props.style).toContainEqual(customStyle);
    expect(getByText('Click Me').props.style).toContainEqual(customTextStyle);
  });
});

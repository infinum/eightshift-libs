import classnames from 'classnames';
import { __ } from '@wordpress/i18n';
import { RichText } from '@wordpress/editor';

export const ButtonEditor = (props) => {
  const {
    blockClass,
    title,
    onChangeTitle,
    styleSize,
    styleColor,
    styleSizeWidth,
  } = props;

  const componentClass = 'btn';

  const buttonClass = classnames([
    componentClass,
    `${componentClass}__size--${styleSize}`,
    `${componentClass}__color--${styleColor}`,
    `${componentClass}__size-width--${styleSizeWidth}`,
    `${blockClass}__btn`,
  ]);

  return (
    <RichText
      placeholder={__('Add Button Title', 'eightshift-boilerplate')}
      value={title}
      onChange={onChangeTitle}
      className={buttonClass}
      keepPlaceholderOnFocus
    />
  );
};

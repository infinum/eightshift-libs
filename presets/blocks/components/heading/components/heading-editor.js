import classnames from 'classnames';
import { __ } from '@wordpress/i18n';
import { RichText } from '@wordpress/editor';

export const HeadingEditor = (props) => {
  const {
    blockClass,
    content,
    onChangeContent,
    level,
    styleAlign,
    styleColor,
    styleSize,
  } = props;

  const componentClass = 'heading';

  const headingClass = classnames([
    componentClass,
    `${componentClass}__align--${styleAlign}`,
    `${componentClass}__color--${styleColor}`,
    `${componentClass}__size--${styleSize}`,
    `${blockClass}__heading`,
  ]);

  return (
    <RichText
      className={headingClass}
      placeholder={__('Add your heading', 'eightshift-block')}
      onChange={onChangeContent}
      value={content}
    />
  );
};

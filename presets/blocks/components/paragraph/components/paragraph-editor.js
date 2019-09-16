import classnames from 'classnames';
import { __ } from '@wordpress/i18n';
import { RichText } from '@wordpress/editor';

export const ParagraphEditor = (props) => {
  const {
    blockClass,
    content,
    onChangeContent,
    styleAlign,
    styleColor,
    removeStyle,
  } = props;

  const componentClass = 'paragraph';

  const paragraphClass = classnames([
    componentClass,
    `${componentClass}__color--${styleColor}`,
    `${componentClass}__align--${styleAlign}`,
    `${blockClass}__paragraph`,
  ]);

  return (
    <RichText
      className={removeStyle ? '' : paragraphClass}
      placeholder={__('Add your paragraph', 'eightshift-block')}
      onChange={onChangeContent}
      value={content}
    />
  );
};

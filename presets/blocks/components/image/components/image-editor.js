import classnames from 'classnames';

export const ImageEditor = (props) => {
  const {
    blockClass,
    url,
  } = props;

  const componentClass = 'image';

  const imageClass = classnames([
    componentClass,
    `${blockClass}__img`,
  ]);

  return (
    <img className={imageClass} src={url} alt="" />
  );
};

import classnames from 'classnames';

export const VideoEditor = (props) => {
  const {
    blockClass,
    url,
  } = props;

  const componentClass = 'video';

  const videoClass = classnames([
    componentClass,
    `${blockClass}__video`,
  ]);

  return (
    <video className={videoClass} autoPlay loop muted>
      <source src={url} type="video/mp4" />
    </video>
  );
};

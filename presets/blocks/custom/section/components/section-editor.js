import { InnerBlocks } from '@wordpress/editor';

export const SectionEditor = (props) => {
  const {
    attributes: {
      blockClass,
    },
  } = props;

  return (
    <div className={blockClass}>
      <InnerBlocks />
    </div>
  );
};

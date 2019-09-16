import { Fragment } from '@wordpress/element';

import { SectionEditor } from './components/section-editor';

export const Section = (props) => {
  const {
    attributes,
  } = props;

  return (
    <Fragment>
      <SectionEditor
        attributes={attributes}
      />
    </Fragment>
  );
};

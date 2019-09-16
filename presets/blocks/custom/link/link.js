import { Fragment } from '@wordpress/element';
import { InspectorControls } from '@wordpress/editor';

import { getActions } from 'EighshiftBlocksGetActions';
import manifest from './manifest.json';

import { LinkEditor } from '../../components/link/components/link-editor';
import { LinkOptions } from '../../components/link/components/link-options';

export const Link = (props) => {
  const {
    attributes: {
      blockClass,
      title,
      url,
      styleColor,
    },
  } = props;

  const actions = getActions(props, manifest);

  return (
    <Fragment>
      <InspectorControls>
        <LinkOptions
          url={url}
          onChangeUrl={actions.onChangeUrl}
          styleColor={styleColor}
          onChangeColor={actions.onChangeColor}
        />
      </InspectorControls>
      <LinkEditor
        blockClass={blockClass}
        title={title}
        onChangeTitle={actions.onChangeTitle}
        styleColor={styleColor}
      />
    </Fragment>
  );
};

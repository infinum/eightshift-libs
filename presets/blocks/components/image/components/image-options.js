import { __ } from '@wordpress/i18n';
import { MediaPlaceholder } from '@wordpress/editor';
import { PanelBody } from '@wordpress/components';

export const ImageOptions = (props) => {
  const {
    onChangeMedia,
  } = props;

  return (
    <PanelBody title={__('Image Details', 'eightshift-boilerplate')}>
      {onChangeMedia &&
        <div className="components-base-control">
          <label className="components-base-control__label" htmlFor="url">{__('Image', 'eightshift-boilerplate')}</label>
          <MediaPlaceholder
            icon="format-image"
            onSelect={onChangeMedia}
            accept={'image/*'}
            allowedTypes={['image', 'application/json']}
          />
        </div>
      }
    </PanelBody>
  );
};

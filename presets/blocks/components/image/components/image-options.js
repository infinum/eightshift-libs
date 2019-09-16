import { __ } from '@wordpress/i18n';
import { MediaPlaceholder } from '@wordpress/editor';
import { PanelBody } from '@wordpress/components';

export const ImageOptions = (props) => {
  const {
    onChangeMedia,
  } = props;

  return (
    <PanelBody title={__('Image Settings', 'eightshift-boilerplate')}>
      {onChangeMedia &&
        <div>
          <label htmlFor="media">{__('Image', 'eightshift-boilerplate')}</label>
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

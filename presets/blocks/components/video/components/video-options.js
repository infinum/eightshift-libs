import { __ } from '@wordpress/i18n';
import { MediaPlaceholder } from '@wordpress/editor';
import { PanelBody } from '@wordpress/components';

export const VideoOptions = (props) => {
  const {
    onChangeMedia,
  } = props;

  return (
    <PanelBody title={__('Video Settings', 'eightshift-boilerplate')}>
      {onChangeMedia &&
        <div>
          <label htmlFor="media">{__('Video', 'eightshift-boilerplate')}</label>
          <MediaPlaceholder
            onSelect={onChangeMedia}
            accept={'video/*'}
            allowedTypes={['video']}
          />
        </div>
      }
    </PanelBody>
  );
};

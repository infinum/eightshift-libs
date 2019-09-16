import { __ } from '@wordpress/i18n';
import { URLInput } from '@wordpress/editor';
import { PanelBody, SelectControl } from '@wordpress/components';

export const LinkOptions = (props) => {
  const {
    url,
    onChangeUrl,
    styleColor,
    onChangeColor,
  } = props;

  return (
    <PanelBody title={__('Link Details', 'eightshift-boilerplate')}>

      {styleColor &&
        <SelectControl
          label={__('Link Color', 'eightshift-boilerplate')}
          value={styleColor}
          options={[
            { label: __('Default', 'eightshift-boilerplate'), value: 'default' },
          ]}
          onChange={onChangeColor}
        />
      }

      {onChangeUrl &&
        <div>
          <label htmlFor="url">{__('Link Url', 'eightshift-boilerplate')}</label>
          <URLInput
            value={url}
            onChange={onChangeUrl}
          />
        </div>
      }

    </PanelBody>
  );
};

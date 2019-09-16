import { __ } from '@wordpress/i18n';
import { URLInput } from '@wordpress/editor';
import { PanelBody, SelectControl, TextControl } from '@wordpress/components';

export const ButtonOptions = (props) => {
  const {
    url,
    onChangeUrl,
    styleSize,
    onChangeStyleSize,
    styleColor,
    onChangeStyleColor,
    styleSizeWidth,
    onChangeStyleSizeWidth,
    id,
    onChangeId,
  } = props;

  return (
    <PanelBody title={__('Button Details', 'eightshift-boilerplate')}>

      {styleColor &&
        <SelectControl
          label={__('Button Color', 'eightshift-boilerplate')}
          value={styleColor}
          options={[
            { label: __('Default', 'eightshift-boilerplate'), value: 'default' },
            { label: __('Primary', 'eightshift-boilerplate'), value: 'primary' },
          ]}
          onChange={onChangeStyleColor}
        />
      }

      {styleSize &&
        <SelectControl
          label={__('Button Size', 'eightshift-boilerplate')}
          value={styleSize}
          options={[
            { label: __('Default', 'eightshift-boilerplate'), value: 'default' },
            { label: __('Big', 'eightshift-boilerplate'), value: 'big' },
          ]}
          onChange={onChangeStyleSize}
        />
      }

      {styleSizeWidth &&
        <SelectControl
          label={__('Button Size Width', 'eightshift-boilerplate')}
          value={styleSizeWidth}
          options={[
            { label: __('Default', 'eightshift-boilerplate'), value: 'default' },
            { label: __('Block', 'eightshift-boilerplate'), value: 'block' },
          ]}
          onChange={onChangeStyleSizeWidth}
        />
      }

      {onChangeUrl &&
        <div>
          <label htmlFor="url">{__('Button Link', 'eightshift-boilerplate')}</label>
          <URLInput
            value={url}
            onChange={onChangeUrl}
          />
          <br />
        </div>
      }

      {onChangeId &&
        <div>
          <TextControl
            label={__('Button ID', 'eightshift-boilerplate')}
            value={id}
            onChange={onChangeId}
          />
        </div>
      }

    </PanelBody>
  );
};

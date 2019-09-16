import { __ } from '@wordpress/i18n';
import { PanelBody, TextControl, SelectControl, ToggleControl } from '@wordpress/components';

export const WrapperOptions = (props) => {
  const {
    attributes: {
      styleBackgroundColor,
      styleTextColor,
      styleContentWidth,
      styleContentOffset,
      styleContainerWidth,
      styleContainerSpacing,
      styleSpacingTop,
      styleSpacingTopTablet,
      styleSpacingTopMobile,
      styleSpacingBottom,
      styleSpacingBottomTablet,
      styleSpacingBottomMobile,
      styleShowOnlyMobile,
      id,
    },
    actions: {
      onChangeStyleBackgroundColor,
      onChangeStyleTextColor,
      onChangeStyleContentWidth,
      onChangeStyleContentOffset,
      onChangeStyleContainerWidth,
      onChangeStyleContainerSpacing,
      onChangeStyleSpacingTop,
      onChangeStyleSpacingTopTablet,
      onChangeStyleSpacingTopMobile,
      onChangeStyleSpacingBottom,
      onChangeStyleSpacingBottomTablet,
      onChangeStyleSpacingBottomMobile,
      onChangeStyleShowOnlyMobile,
      onChangeId,
    },
  } = props;

  const maxCols = 12;
  const colsOutput = [];

  for (let index = 1; index <= maxCols; index++) {
    colsOutput.push({ label: `${index} - (${Math.round((100 / maxCols) * index)}%)`, value: index });
  }

  const spacingOptions = [
    { label: __('Not Set', 'eightshift-boilerplate'), value: '' },
    { label: __('Biggest (100px)', 'eightshift-boilerplate'), value: 'biggest' },
    { label: __('Bigger (90px)', 'eightshift-boilerplate'), value: 'bigger' },
    { label: __('Big (80px)', 'eightshift-boilerplate'), value: 'big' },
    { label: __('Largest (70px)', 'eightshift-boilerplate'), value: 'largest' },
    { label: __('Larger (60px)', 'eightshift-boilerplate'), value: 'larger' },
    { label: __('Large (50px)', 'eightshift-boilerplate'), value: 'large' },
    { label: __('Default (40px)', 'eightshift-boilerplate'), value: 'default' },
    { label: __('Medium (30px)', 'eightshift-boilerplate'), value: 'medium' },
    { label: __('Small (20px)', 'eightshift-boilerplate'), value: 'small' },
    { label: __('Tiny (10px)', 'eightshift-boilerplate'), value: 'tiny' },
    { label: __('No padding (0px)', 'eightshift-boilerplate'), value: 'no-spacing' },
  ];

  return (
    <PanelBody title={__('Utility', 'eightshift-boilerplate')}>
      <h3>{__('Colors', 'eightshift-boilerplate')}</h3>

      {onChangeStyleBackgroundColor &&
        <SelectControl
          label={__('Background Color', 'eightshift-boilerplate')}
          value={styleBackgroundColor}
          options={[
            { label: __('Default', 'eightshift-boilerplate'), value: 'default' },
            { label: __('Primary', 'eightshift-boilerplate'), value: 'primary' },
            { label: __('Black', 'eightshift-boilerplate'), value: 'black' },
          ]}
          onChange={onChangeStyleBackgroundColor}
        />
      }

      {onChangeStyleTextColor &&
        <SelectControl
          label={__('Text Color', 'eightshift-boilerplate')}
          value={styleTextColor}
          options={[
            { label: __('Default', 'eightshift-boilerplate'), value: 'default' },
          ]}
          onChange={onChangeStyleTextColor}
        />
      }

      <hr />
      <h3>{__('Content', 'eightshift-boilerplate')}</h3>

      {onChangeStyleContentWidth &&
        <SelectControl
          label={__('Content Width', 'eightshift-boilerplate')}
          value={styleContentWidth}
          options={colsOutput}
          onChange={onChangeStyleContentWidth}
        />
      }

      {onChangeStyleContentOffset &&
        <SelectControl
          label={__('Content Offset', 'eightshift-boilerplate')}
          value={styleContentOffset}
          options={[
            { label: __('No offset', 'eightshift-boilerplate'), value: 'none' },
            { label: __('Center', 'eightshift-boilerplate'), value: 'center' },
          ]}
          onChange={onChangeStyleContentOffset}
        />
      }

      <hr />
      <h3>{__('Container', 'eightshift-boilerplate')}</h3>
      {onChangeStyleContainerWidth &&
        <SelectControl
          label={__('Container Width', 'eightshift-boilerplate')}
          value={styleContainerWidth}
          options={[
            { label: __('Default', 'eightshift-boilerplate'), value: 'default' },
            { label: __('Medium', 'eightshift-boilerplate'), value: 'medium' },
            { label: __('No Width', 'eightshift-boilerplate'), value: 'no-width' },
          ]}
          onChange={onChangeStyleContainerWidth}
        />
      }

      {onChangeStyleContainerSpacing &&
        <SelectControl
          label={__('Container Spacing', 'eightshift-boilerplate')}
          value={styleContainerSpacing}
          options={[
            { label: __('Default', 'eightshift-boilerplate'), value: 'default' },
            { label: __('No Spacing', 'eightshift-boilerplate'), value: 'no-spacing' },
          ]}
          onChange={onChangeStyleContainerSpacing}
        />
      }

      <hr />
      <h3>{__('Spacing TOP', 'eightshift-boilerplate')}</h3>

      {onChangeStyleSpacingTop &&
        <SelectControl
          label={__('Desktop', 'eightshift-boilerplate')}
          value={styleSpacingTop}
          options={spacingOptions}
          onChange={onChangeStyleSpacingTop}
        />
      }

      {onChangeStyleSpacingTopTablet &&
        <SelectControl
          label={__('Tablet', 'eightshift-boilerplate')}
          value={styleSpacingTopTablet}
          options={spacingOptions}
          onChange={onChangeStyleSpacingTopTablet}
        />
      }

      {onChangeStyleSpacingTopMobile &&
        <SelectControl
          label={__('Mobile', 'eightshift-boilerplate')}
          value={styleSpacingTopMobile}
          options={spacingOptions}
          onChange={onChangeStyleSpacingTopMobile}
        />
      }

      <hr />
      <h3>{__('Spacing BOTTOM', 'eightshift-boilerplate')}</h3>
      {onChangeStyleSpacingBottom &&
        <SelectControl
          label={__('Desktop', 'eightshift-boilerplate')}
          value={styleSpacingBottom}
          options={spacingOptions}
          onChange={onChangeStyleSpacingBottom}
        />
      }

      {onChangeStyleSpacingBottomTablet &&
        <SelectControl
          label={__('Tablet', 'eightshift-boilerplate')}
          value={styleSpacingBottomTablet}
          options={spacingOptions}
          onChange={onChangeStyleSpacingBottomTablet}
        />
      }

      {onChangeStyleSpacingBottomMobile &&
        <SelectControl
          label={__('Mobile', 'eightshift-boilerplate')}
          value={styleSpacingBottomMobile}
          options={spacingOptions}
          onChange={onChangeStyleSpacingBottomMobile}
        />
      }

      <hr />
      <h3>{__('Visibility', 'eightshift-boilerplate')}</h3>
      {onChangeStyleShowOnlyMobile &&
        <ToggleControl
          label={__('Show Block Only On Mobile', 'eightshift-boilerplate')}
          checked={styleShowOnlyMobile}
          onChange={onChangeStyleShowOnlyMobile}
        />
      }
      
      <hr />
      <h3>{__('General', 'eightshift-boilerplate')}</h3>
      {onChangeId &&
        <TextControl
          label={__('Section ID', 'eightshift-boilerplate')}
          value={id}
          onChange={onChangeId}
        />
      }
    </PanelBody>
  );
};

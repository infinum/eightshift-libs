import classnames from 'classnames';

export const WrapperEditor = (props) => {
  const {
    children,
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
  } = props;

  const wrapperMainClass = 'wrapper';

  const wrapperClass = classnames([
    wrapperMainClass,
    `${wrapperMainClass}__bg-color--${styleBackgroundColor}`,
    `${wrapperMainClass}__text-color--${styleTextColor}`,
    `${wrapperMainClass}__width--${styleContentWidth}`,
    `${wrapperMainClass}__spacing-top--${styleSpacingTop}`,
    `${wrapperMainClass}__spacing-top-tablet--${styleSpacingTopTablet}`,
    `${wrapperMainClass}__spacing-top-mobile--${styleSpacingTopMobile}`,
    `${wrapperMainClass}__spacing-bottom--${styleSpacingBottom}`,
    `${wrapperMainClass}__spacing-bottom-tablet--${styleSpacingBottomTablet}`,
    `${wrapperMainClass}__spacing-bottom-mobile--${styleSpacingBottomMobile}`,
    `${wrapperMainClass}__show-only-mobile--${styleShowOnlyMobile}`,
  ]);

  const wrapperContainerClass = classnames([
    `${wrapperMainClass}__container`,
    `${wrapperMainClass}__container-width--${styleContainerWidth}`,
    `${wrapperMainClass}__container-spacing--${styleContainerSpacing}`,
  ]);

  const wrapperInnerClass = classnames([
    `${wrapperMainClass}__inner`,
    `${wrapperMainClass}__inner-offset--${styleContentOffset}`,
  ]);

  return (
    <div className={wrapperClass} id={id}>
      <div className={wrapperContainerClass}>
        <div className={wrapperInnerClass}>
          {children}
        </div>
      </div>
    </div>
  );
};

//
//  SelectSingleViewController.h
//  open311
//
//  Created by Cliff Ingham on 9/26/11.
//  Copyright 2011 City of Bloomington. All rights reserved.
//

#import <UIKit/UIKit.h>
#import "BaseFieldViewController.h"

@interface SelectSingleViewController : BaseFieldViewController <UIPickerViewDataSource, UIPickerViewDelegate> {
    
    IBOutlet UIPickerView *picker;
    NSArray *values;
}

@end

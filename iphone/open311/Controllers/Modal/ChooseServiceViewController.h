//
//  ChooseServiceViewController.h
//  open311
//
//  Created by Cliff Ingham on 9/12/11.
//  Copyright 2011 City of Bloomington. All rights reserved.
//

#import <UIKit/UIKit.h>


@interface ChooseServiceViewController : UIViewController <UIPickerViewDelegate, UIPickerViewDataSource> {
    
}
@property (nonatomic, retain) NSDictionary *chosenService;

@end

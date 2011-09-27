//
//  TextFieldViewController.h
//  open311
//
//  Created by Cliff Ingham on 9/15/11.
//  Copyright 2011 City of Bloomington. All rights reserved.
//

#import <UIKit/UIKit.h>
#import "BaseFieldViewController.h"


@interface TextFieldViewController : BaseFieldViewController {
    
    IBOutlet UITextView *textarea;
}

@end

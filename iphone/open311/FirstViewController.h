//
//  FirstViewController.h
//  open311
//
//  Created by Cliff Ingham on 8/24/11.
//  Copyright 2011 City of Bloomington. All rights reserved.
//

#import <UIKit/UIKit.h>


@interface FirstViewController : UIViewController <UITextFieldDelegate> {

    UITextField *open311URL;
}
@property (nonatomic, retain) IBOutlet UITextField *open311URL;

@end

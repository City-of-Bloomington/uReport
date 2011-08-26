//
//  SettingsViewController.h
//  open311
//
//  Created by Cliff Ingham on 8/26/11.
//  Copyright 2011 City of Bloomington. All rights reserved.
//

#import <UIKit/UIKit.h>

@interface SettingsViewController : UIViewController {
    UITextField *open311URL;
}
@property (nonatomic, retain) IBOutlet UITextField *open311URL;

@end

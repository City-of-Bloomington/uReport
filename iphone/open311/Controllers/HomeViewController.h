//
//  HomeViewController.h
//  open311
//
//  Created by Cliff Ingham on 9/6/11.
//  Copyright 2011 City of Bloomington. All rights reserved.
//

#import <UIKit/UIKit.h>


@interface HomeViewController : UIViewController {
    
    UINavigationItem *navigationBar;
}
@property (nonatomic, retain) IBOutlet UINavigationItem *navigationBar;
- (IBAction)goToNewReport:(id)sender;
- (IBAction)goToIssues:(id)sender;

@end

//
//  MyServersViewController.h
//  open311
//
//  Created by Cliff Ingham on 9/6/11.
//  Copyright 2011 City of Bloomington. All rights reserved.
//

#import <UIKit/UIKit.h>


@interface MyServersViewController : UIViewController <UITableViewDelegate, UITableViewDataSource> {
    
    UITableView *myServersTableView;
}
@property (nonatomic, retain) IBOutlet UITableView *myServersTableView;
- (void) goToAvailableServers;
@end

//
//  MyReportsViewController.h
//  open311
//
//  Created by Cliff Ingham on 10/6/11.
//  Copyright 2011 City of Bloomington. All rights reserved.
//

#import <UIKit/UIKit.h>

@interface MyReportsViewController : UIViewController {
    UITableView *myReqestsTable;
}

@property (nonatomic, retain) IBOutlet UITableView *myRequestsTable;

@end

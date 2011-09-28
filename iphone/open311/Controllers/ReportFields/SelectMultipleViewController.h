//
//  SelectMultipleViewController.h
//  open311
//
//  Created by Cliff Ingham on 9/27/11.
//  Copyright 2011 City of Bloomington. All rights reserved.
//

#import <UIKit/UIKit.h>
#import "BaseFieldViewController.h"

@interface SelectMultipleViewController : BaseFieldViewController <UITableViewDelegate, UITableViewDataSource> {
    NSArray *values;
    IBOutlet UITableView *table;
}

@end

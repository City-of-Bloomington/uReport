//
//  StringFieldViewController.m
//  open311
//
//  Created by Cliff Ingham on 9/26/11.
//  Copyright 2011 City of Bloomington. All rights reserved.
//

#import "StringFieldViewController.h"

@implementation StringFieldViewController

- (void)dealloc {
    [input release];
    [super dealloc];
}

#pragma mark - Button handling functions
- (void)done
{
    [[self.reportForm objectForKey:@"data"] setObject:input.text forKey:self.fieldname];
    [super done];
}
#pragma mark - View lifecycle

- (void)viewDidUnload
{
    [input release];
    input = nil;
    [super viewDidUnload];
}

- (void)viewWillAppear:(BOOL)animated
{
    input.text = [[self.reportForm objectForKey:@"data"] objectForKey:self.fieldname];
    [super viewWillAppear:animated];
}


@end

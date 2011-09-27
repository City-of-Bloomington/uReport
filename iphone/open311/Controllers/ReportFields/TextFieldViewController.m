//
//  TextFieldViewController.m
//  open311
//
//  Created by Cliff Ingham on 9/15/11.
//  Copyright 2011 City of Bloomington. All rights reserved.
//

#import "TextFieldViewController.h"


@implementation TextFieldViewController

- (void)dealloc
{
    [textarea release];
    [super dealloc];
}

#pragma mark - View lifecycle

- (void)viewDidUnload
{
    [textarea release];
    textarea = nil;
    [super viewDidUnload];
    // Release any retained subviews of the main view.
    // e.g. self.myOutlet = nil;
}

- (void)viewWillAppear:(BOOL)animated
{
    textarea.text = [[self.reportForm objectForKey:@"data"] objectForKey:self.fieldname];
    [super viewWillAppear:animated];
}


#pragma mark - Button handling functions
/**
 * Saves changes to the text and send them back to the report
 */
- (void)done
{
    [[self.reportForm objectForKey:@"data"] setObject:textarea.text forKey:self.fieldname];
    [super done];
}

@end

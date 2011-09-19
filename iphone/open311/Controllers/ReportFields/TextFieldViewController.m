//
//  TextFieldViewController.m
//  open311
//
//  Created by Cliff Ingham on 9/15/11.
//  Copyright 2011 City of Bloomington. All rights reserved.
//

#import "TextFieldViewController.h"


@implementation TextFieldViewController
@synthesize fieldname;
@synthesize reportForm;

- (id)initWithFieldname:(NSString *)field report:(NSMutableDictionary *)report
{
    self = [super init];
    if (self) {
        self.reportForm = report;
        self.fieldname = field;
    }
    return self;
}

- (id)initWithNibName:(NSString *)nibNameOrNil bundle:(NSBundle *)nibBundleOrNil
{
    self = [super initWithNibName:nibNameOrNil bundle:nibBundleOrNil];
    if (self) {
        // Custom initialization
    }
    return self;
}

- (void)dealloc
{
    [fieldname release];
    [label release];
    [textarea release];
    [super dealloc];
}

- (void)didReceiveMemoryWarning
{
    // Releases the view if it doesn't have a superview.
    [super didReceiveMemoryWarning];
    
    // Release any cached data, images, etc that aren't in use.
}

#pragma mark - View lifecycle

- (void)viewDidLoad
{
    [super viewDidLoad];
    // Do any additional setup after loading the view from its nib.
}

- (void)viewDidUnload
{
    [label release];
    label = nil;
    [textarea release];
    textarea = nil;
    [super viewDidUnload];
    // Release any retained subviews of the main view.
    // e.g. self.myOutlet = nil;
}

- (void)viewWillAppear:(BOOL)animated
{
    label.text = [[self.reportForm objectForKey:@"labels"] objectForKey:self.fieldname];
    textarea.text = [[self.reportForm objectForKey:@"data"] objectForKey:self.fieldname];
    [super viewWillAppear:animated];
}

- (void)viewWillDisappear:(BOOL)animated
{
    [[self.reportForm objectForKey:@"data"] setObject:textarea.text forKey:self.fieldname];
    [super viewWillDisappear:animated];
}

- (BOOL)shouldAutorotateToInterfaceOrientation:(UIInterfaceOrientation)interfaceOrientation
{
    // Return YES for supported orientations
    return (interfaceOrientation == UIInterfaceOrientationPortrait);
}

@end

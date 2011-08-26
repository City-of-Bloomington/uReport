//
//  SettingsViewController.m
//  open311
//
//  Created by Cliff Ingham on 8/26/11.
//  Copyright 2011 City of Bloomington. All rights reserved.
//

#import "SettingsViewController.h"


@implementation SettingsViewController
@synthesize open311URL;

- (id)initWithNibName:(NSString *)nibNameOrNil bundle:(NSBundle *)nibBundleOrNil
{
    self = [super initWithNibName:nibNameOrNil bundle:nibBundleOrNil];
    if (self) {
        // Custom initialization
        [[self tabBarItem] setTitle:@"Settings"];
        [[self tabBarItem] setImage:[UIImage imageNamed:@"iconSettings.png"]];
    }
    return self;
}

- (void)dealloc
{
    [open311URL release];
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
    [self setOpen311URL:nil];
    [super viewDidUnload];
    // Release any retained subviews of the main view.
    // e.g. self.myOutlet = nil;
}

- (BOOL)shouldAutorotateToInterfaceOrientation:(UIInterfaceOrientation)interfaceOrientation
{
    // Return YES for supported orientations
    return (interfaceOrientation == UIInterfaceOrientationPortrait);
}

- (void)viewWillAppear:(BOOL)animated
{
    open311URL.text = [[NSUserDefaults standardUserDefaults] stringForKey:@"open311URL"];
    [super viewWillAppear:animated];
}

- (void)viewWillDisappear:(BOOL)animated
{
    [[NSUserDefaults standardUserDefaults] setValue:open311URL.text forKey:@"open311URL"];
    [super viewWillDisappear:animated];
}

-(BOOL)textFieldShouldReturn:(UITextField *)textField
{
    [textField resignFirstResponder];
    return true;
}

@end

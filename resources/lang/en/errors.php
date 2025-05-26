<?php

return array (
  'areYouSure' => 'Are you sure?',
  'deleteWarning' => 'You will not be able to recover the deleted record!',
  'fieldRequired' => 'field is required',
  'alreadyTaken' => 'has already been taken. Try another.',
  'nexmoKeyRequired' => 'Nexmo Key is required for Active Status',
  'nexmoSecretRequired' => 'Nexmo Secret is required for Active Status',
  'nexmoFromRequired' => 'Nexmo From is required for Active Status',
  'coupon' => 
  array (
    'required' => 'Coupon code can not be blank',
    'serviceRequired' => 'Add atleast one service to cart.',
    'customerRequired' => 'Select customer to continue.',
  ),
  'bookingTime' => 
  array (
    'startTime' => 
    array (
      'dateFormat' => 'Open Time must be in format 09:00 AM.',
      'requiredIf' => 'Open Time is required when :other is :value.',
    ),
    'endTime' => 
    array (
      'dateFormat' => 'Close Time must be in format 09:00 AM.',
    ),
    'slotDuration' => 
    array (
      'integer' => 'Slot Duration must be an integer.',
      'requiredIf' => 'Slot Duration is required when :other is :value.',
      'min' => 'Minimum value of Slot Duration must be 1.',
    ),
    'maxBooking' => 
    array (
      'integer' => 'Maximum Number of Booking must be an integer.',
      'requiredIf' => 'Maximum Number of Booking is required when :other is :value.',
      'min' => 'Minimum value of Maximum Number of Booking must be 0.',
    ),
  ),
  'payment' => 
  array (
    'requiredIf' => 'The :attribute field is required when status is active',
  ),
);

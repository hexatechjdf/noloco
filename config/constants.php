<?php

return [
    'chatgpt_prompt'=>'You are a PDF analyst, and your job is to analyze the current provided pdf, and extract the page 9 data, there is accurate data on page no 9, you should provide me exact table data, just return only json block in one message no extra text, also no need to write ```json or ``` and do not give any citation got it? , make sure it returns all payoffs as one array as a object not as separate 1st payoff, 2nd payoff , just return json array object and object keys should follow this pattern {
      "title": "New Vehicle",
      "payment_date": "10/2028",
      "payoff_amount": 12355,
      "payment_ratio": "51 / 76",
      "interest_saved": 1253,
      "tailwind_increase": 530,
      "cumulative_tailwind": 530
    }, if returns not an array as a object, return empty'
];

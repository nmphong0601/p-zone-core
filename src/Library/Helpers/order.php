<?php
use PZone\Core\Front\Models\ShopCountry;

/**
 * Function process after order success
 */
if (!function_exists('pz_order_process_after_success') && !in_array('pz_order_process_after_success', config('helper_except', []))) {
    function pz_order_process_after_success(string $orderID = ""):array
    {
        $templatePath = 'templates.' . pz_store('template');
        if ((pz_config('order_success_to_admin') || pz_config('order_success_to_customer')) && pz_config('email_action_mode')) {
            $data = \PZone\Core\Front\Models\ShopOrder::with('details')->find($orderID)->toArray();
            $checkContent = (new \PZone\Core\Front\Models\ShopEmailTemplate)->where('group', 'order_success_to_admin')->where('status', 1)->first();
            $checkContentCustomer = (new \PZone\Core\Front\Models\ShopEmailTemplate)->where('group', 'order_success_to_customer')->where('status', 1)->first();
            if ($checkContent || $checkContentCustomer) {
                $orderDetail = '';
                $orderDetail .= '<tr>
                                    <td>' . pz_language_render('email.order.sort') . '</td>
                                    <td>' . pz_language_render('email.order.sku') . '</td>
                                    <td>' . pz_language_render('email.order.name') . '</td>
                                    <td>' . pz_language_render('email.order.price') . '</td>
                                    <td>' . pz_language_render('email.order.qty') . '</td>
                                    <td>' . pz_language_render('email.order.total') . '</td>
                                </tr>';
                foreach ($data['details'] as $key => $detail) {
                    $product = (new \PZone\Core\Front\Models\ShopProduct)->getDetail($detail['product_id']);
                    $pathDownload = $product->downloadPath->path ?? '';
                    $nameProduct = $detail['name'];
                    if ($product && $pathDownload && $product->property == PZ_PROPERTY_DOWNLOAD) {
                        $nameProduct .="<br><a href='".pz_path_download_render($pathDownload)."'>Download</a>";
                    }

                    $orderDetail .= '<tr>
                                    <td>' . ($key + 1) . '</td>
                                    <td>' . $detail['sku'] . '</td>
                                    <td>' . $nameProduct . '</td>
                                    <td>' . pz_currency_render($detail['price'], '', '', '', false) . '</td>
                                    <td>' . number_format($detail['qty']) . '</td>
                                    <td align="right">' . pz_currency_render($detail['total_price'], '', '', '', false) . '</td>
                                </tr>';
                }
                $dataFind = [
                    '/\{\{\$title\}\}/',
                    '/\{\{\$orderID\}\}/',
                    '/\{\{\$firstName\}\}/',
                    '/\{\{\$lastName\}\}/',
                    '/\{\{\$toname\}\}/',
                    '/\{\{\$address\}\}/',
                    '/\{\{\$address1\}\}/',
                    '/\{\{\$address2\}\}/',
                    '/\{\{\$address3\}\}/',
                    '/\{\{\$email\}\}/',
                    '/\{\{\$phone\}\}/',
                    '/\{\{\$comment\}\}/',
                    '/\{\{\$orderDetail\}\}/',
                    '/\{\{\$subtotal\}\}/',
                    '/\{\{\$shipping\}\}/',
                    '/\{\{\$discount\}\}/',
                    '/\{\{\$otherFee\}\}/',
                    '/\{\{\$total\}\}/',
                ];
                $dataReplace = [
                    pz_language_render('email.order.email_subject_customer') . '#' . $orderID,
                    $orderID,
                    $data['first_name'],
                    $data['last_name'],
                    $data['first_name'].' '.$data['last_name'],
                    $data['address1'] . ' ' . $data['address2'].' '.$data['address3'],
                    $data['address1'],
                    $data['address2'],
                    $data['address3'],
                    $data['email'],
                    $data['phone'],
                    $data['comment'],
                    $orderDetail,
                    pz_currency_render($data['subtotal'], '', '', '', false),
                    pz_currency_render($data['shipping'], '', '', '', false),
                    pz_currency_render($data['discount'], '', '', '', false),
                    pz_currency_render($data['other_fee'], '', '', '', false),
                    pz_currency_render($data['total'], '', '', '', false),
                ];

                // Send mail order success to admin
                if (pz_config('order_success_to_admin') && $checkContent) {
                    $content = $checkContent->text;
                    $content = preg_replace($dataFind, $dataReplace, $content);
                    $dataView = [
                        'content' => $content,
                    ];
                    $config = [
                        'to' => pz_store('email'),
                        'subject' => pz_language_render('email.order.email_subject_to_admin', ['order_id' => $orderID]),
                    ];
                    pz_send_mail($templatePath . '.mail.order_success_to_admin', $dataView, $config, []);
                }

                // Send mail order success to customer
                if (pz_config('order_success_to_customer') && $checkContentCustomer && $data['email']) {
                    $contentCustomer = $checkContentCustomer->text;
                    $contentCustomer = preg_replace($dataFind, $dataReplace, $contentCustomer);
                    $dataView = [
                        'content' => $contentCustomer,
                    ];
                    $config = [
                        'to' => $data['email'],
                        'replyTo' => pz_store('email'),
                        'subject' => pz_language_render('email.order.email_subject_customer', ['order_id' => $orderID]),
                    ];

                    $attach = [];
                    if (pz_config('order_success_to_customer_pdf')) {
                        // Invoice pdf
                        \PDF::loadView($templatePath . '.mail.order_success_to_customer_pdf', $dataView)
                            ->save(\Storage::disk('invoice')->path('order-'.$orderID.'.pdf'));
                        $attach['attachFromStorage'] = [
                            [
                                'file_storage' => 'invoice',
                                'file_path' => 'order-'.$orderID.'.pdf',
                            ]
                        ];
                    }

                    pz_send_mail($templatePath . '.mail.order_success_to_customer', $dataView, $config, $attach);
                }
            }
        }
        $dataResponse = [
            'orderID'        => $orderID,
        ];
        return $dataResponse;
    }
}

/**
 * Function process mapping validate order
 */
if (!function_exists('pz_order_mapping_validate') && !in_array('pz_order_mapping_validate', config('helper_except', []))) {
    function pz_order_mapping_validate():array
    {
        $validate = [
            'first_name'     => config('validation.customer.first_name', 'required|string|max:100'),
            'email'          => config('validation.customer.email', 'required|string|email|max:255'),
        ];
        //check shipping
        if (!pz_config('shipping_off')) {
            $validate['shippingMethod'] = 'required';
        }
        //check payment
        if (!pz_config('payment_off')) {
            $validate['paymentMethod'] = 'required';
        }

        if (pz_config('customer_lastname')) {
            if (pz_config('customer_lastname_required')) {
                $validate['last_name'] = config('validation.customer.last_name_required', 'required|string|max:100');
            } else {
                $validate['last_name'] = config('validation.customer.last_name_null', 'nullable|string|max:100');
            }
        }
        if (pz_config('customer_address1')) {
            if (pz_config('customer_address1_required')) {
                $validate['address1'] = config('validation.customer.address1_required', 'required|string|max:100');
            } else {
                $validate['address1'] = config('validation.customer.address1_null', 'nullable|string|max:100');
            }
        }

        if (pz_config('customer_address2')) {
            if (pz_config('customer_address2_required')) {
                $validate['address2'] = config('validation.customer.address2_required', 'required|string|max:100');
            } else {
                $validate['address2'] = config('validation.customer.address2_null', 'nullable|string|max:100');
            }
        }

        if (pz_config('customer_address3')) {
            if (pz_config('customer_address3_required')) {
                $validate['address3'] = config('validation.customer.address3_required', 'required|string|max:100');
            } else {
                $validate['address3'] = config('validation.customer.address3_null', 'nullable|string|max:100');
            }
        }

        if (pz_config('customer_phone')) {
            if (pz_config('customer_phone_required')) {
                $validate['phone'] = config('validation.customer.phone_required', 'required|regex:/^0[^0][0-9\-]{6,12}$/');
            } else {
                $validate['phone'] = config('validation.customer.phone_null', 'nullable|regex:/^0[^0][0-9\-]{6,12}$/');
            }
        }
        if (pz_config('customer_country')) {
            $arrayCountry = (new ShopCountry)->pluck('code')->toArray();
            if (pz_config('customer_country_required')) {
                $validate['country'] = config('validation.customer.country_required', 'required|string|min:2').'|in:'. implode(',', $arrayCountry);
            } else {
                $validate['country'] = config('validation.customer.country_null', 'nullable|string|min:2').'|in:'. implode(',', $arrayCountry);
            }
        }

        if (pz_config('customer_postcode')) {
            if (pz_config('customer_postcode_required')) {
                $validate['postcode'] = config('validation.customer.postcode_required', 'required|min:5');
            } else {
                $validate['postcode'] = config('validation.customer.postcode_null', 'nullable|min:5');
            }
        }
        if (pz_config('customer_company')) {
            if (pz_config('customer_company_required')) {
                $validate['company'] = config('validation.customer.company_required', 'required|string|max:100');
            } else {
                $validate['company'] = config('validation.customer.company_null', 'nullable|string|max:100');
            }
        }

        if (pz_config('customer_name_kana')) {
            if (pz_config('customer_name_kana_required')) {
                $validate['first_name_kana'] = config('validation.customer.name_kana_required', 'required|string|max:100');
                $validate['last_name_kana'] = config('validation.customer.name_kana_required', 'required|string|max:100');
            } else {
                $validate['first_name_kana'] = config('validation.customer.name_kana_null', 'nullable|string|max:100');
                $validate['last_name_kana'] = config('validation.customer.name_kana_null', 'nullable|string|max:100');
            }
        }

        $messages = [
            'last_name.required'      => pz_language_render('validation.required', ['attribute'=> pz_language_render('cart.last_name')]),
            'first_name.required'     => pz_language_render('validation.required', ['attribute'=> pz_language_render('cart.first_name')]),
            'email.required'          => pz_language_render('validation.required', ['attribute'=> pz_language_render('cart.email')]),
            'address1.required'       => pz_language_render('validation.required', ['attribute'=> pz_language_render('cart.address1')]),
            'address2.required'       => pz_language_render('validation.required', ['attribute'=> pz_language_render('cart.address2')]),
            'address3.required'       => pz_language_render('validation.required', ['attribute'=> pz_language_render('cart.address3')]),
            'phone.required'          => pz_language_render('validation.required', ['attribute'=> pz_language_render('cart.phone')]),
            'country.required'        => pz_language_render('validation.required', ['attribute'=> pz_language_render('cart.country')]),
            'postcode.required'       => pz_language_render('validation.required', ['attribute'=> pz_language_render('cart.postcode')]),
            'company.required'        => pz_language_render('validation.required', ['attribute'=> pz_language_render('cart.company')]),
            'sex.required'            => pz_language_render('validation.required', ['attribute'=> pz_language_render('cart.sex')]),
            'birthday.required'       => pz_language_render('validation.required', ['attribute'=> pz_language_render('cart.birthday')]),
            'email.email'             => pz_language_render('validation.email', ['attribute'=> pz_language_render('cart.email')]),
            'phone.regex'             => pz_language_render('customer.phone_regex'),
            'postcode.min'            => pz_language_render('validation.min', ['attribute'=> pz_language_render('cart.postcode')]),
            'country.min'             => pz_language_render('validation.min', ['attribute'=> pz_language_render('cart.country')]),
            'first_name.max'          => pz_language_render('validation.max', ['attribute'=> pz_language_render('cart.first_name')]),
            'email.max'               => pz_language_render('validation.max', ['attribute'=> pz_language_render('cart.email')]),
            'address1.max'            => pz_language_render('validation.max', ['attribute'=> pz_language_render('cart.address1')]),
            'address2.max'            => pz_language_render('validation.max', ['attribute'=> pz_language_render('cart.address2')]),
            'address3.max'            => pz_language_render('validation.max', ['attribute'=> pz_language_render('cart.address3')]),
            'last_name.max'           => pz_language_render('validation.max', ['attribute'=> pz_language_render('cart.last_name')]),
            'birthday.date'           => pz_language_render('validation.date', ['attribute'=> pz_language_render('cart.birthday')]),
            'birthday.date_format'    => pz_language_render('validation.date_format', ['attribute'=> pz_language_render('cart.birthday')]),
            'shippingMethod.required' => pz_language_render('cart.validation.shippingMethod_required'),
            'paymentMethod.required'  => pz_language_render('cart.validation.paymentMethod_required'),
        ];

        $dataMap['validate'] = $validate;
        $dataMap['messages'] = $messages;

        return $dataMap;
    }
}
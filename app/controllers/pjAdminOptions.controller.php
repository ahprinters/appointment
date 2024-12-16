<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjAdminOptions extends pjAdmin
{
	public function pjActionBooking()
	{
		if (!pjAuth::factory()->hasAccess())
		{
			$this->sendForbidden();
			return;
		}
		
		$pjOptionModel = pjOptionModel::factory();
		
		$arr = $pjOptionModel
				->where('t1.foreign_id', $this->getForeignId())
				->where('t1.tab_id', 1)
				->orderBy('t1.order ASC')
				->findAll()
				->getData();
	
		$this->set('arr', $arr);
		
		$tmp = $pjOptionModel->reset()->where('foreign_id', $this->getForeignId())->findAll()->getData();
		$o_arr = array();
		foreach ($tmp as $item)
		{
			$o_arr[$item['key']] = $item;
		}
		$this->set('o_arr', $o_arr);
		
		$this->appendJs('pjAdminOptions.js');
	}
	
	public function pjActionBookingForm()
	{
		if (!pjAuth::factory()->hasAccess())
		{
			$this->sendForbidden();
			return;
		}
		
		$arr = pjOptionModel::factory()
		->where('t1.foreign_id', $this->getForeignId())
		->where('t1.tab_id', 3)
		->orderBy('t1.order ASC')
		->findAll()
		->getData();
	
		$this->set('arr', $arr);
		$this->appendJs('pjAdminOptions.js');
	}
	
	public function pjActionInstall()
	{
		if (!pjAuth::factory()->hasAccess())
		{
			$this->sendForbidden();
			return;
		}
		
		$pjOptionModel = pjOptionModel::factory();
		
		$o_arr = $pjOptionModel
			->where('t1.foreign_id', $this->getForeignId())
			->where('`key`', 'o_theme')
			->orderBy('t1.key ASC')
			->findAll()
			->getData();
		$this->set('theme_arr', $o_arr[0]);
		
		$o_arr = $pjOptionModel
			->reset()
			->where('t1.foreign_id', $this->getForeignId())
			->where('`key`', 'o_layout')
			->orderBy('t1.key ASC')
			->findAll()
			->getData();

		$this->set('layout_arr', $o_arr[0]);
				
		$this->appendJs('pjAdminOptions.js');
	}
	
	public function pjActionPayments()
	{
		if (!pjAuth::factory()->hasAccess())
		{
			$this->sendForbidden();
			return;
		}
		
		$pjOptionModel = pjOptionModel::factory();
		$arr = $pjOptionModel
				->where('t1.foreign_id', $this->getForeignId())
				->where('tab_id', 2)
				->orderBy('t1.order ASC')
				->findAll()
				->getData();
		$key_arr = 	$pjOptionModel->getDataPair('key');
	
		$this->set('arr', $arr);
		$this->set('key_arr', $key_arr);
		$this->set('o_arr', $pjOptionModel->reset()->getPairs($this->getForeignId()));
	
		$this->setLocalesData();
	
		$this->appendJs('jquery.multilang.js', $this->getConstant('pjBase', 'PLUGIN_JS_PATH'), false, false);
		$this->appendJs('pjAdminOptions.js');
	}
	
	public function pjActionPaymentOptions()
	{
		$this->checkLogin();
	
		$this->setAjax(true);
		
		if (self::isPost() && $this->_post->check('options_update'))
		{
			if (pjObject::getPlugin('pjPayments') !== NULL && $this->_post->check('plugin_payment_options'))
			{
				$this->requestAction(array(
						'controller' => 'pjPayments',
						'action' => 'pjActionSaveOptions',
						'params' => array(
								'foreign_id' => NULL,
								'data' => $this->_post->toArray('plugin_payment_options'),
						)
				), array('return'));
			}
			if(in_array($this->_post->toString('payment_method'), array('cash', 'bank')))
			{
				$pjOptionModel = new pjOptionModel();
				if($this->_post->toString('payment_method') == 'cash')
				{
					$k = 'o_allow_cash';
				}else{
					$k = 'o_allow_bank';
				}
				$value = $this->_post->toString($k) == '1' ? '1|0::1' : '1|0::0';
				$pjOptionModel
				->reset()->debug(1)
				->where('foreign_id', $this->getForeignId())
				->where('`key`', $k)
				->limit(1)
				->modifyAll(array('value' => $value));
			}
			if ($this->_post->check('i18n'))
			{
				pjMultiLangModel::factory()->updateMultiLang($this->_post->toI18n('i18n'), $this->getForeignId(), 'pjPayment', 'data');
			}
			if ($this->_post->check('i18n_options'))
			{
				pjMultiLangModel::factory()->updateMultiLang($this->_post->toI18n('i18n_options'), $this->getForeignId(), 'pjOption', 'data');
			}
		}
		
		if (self::isGet()) 
		{
			$this->set('i18n', pjMultiLangModel::factory()->getMultiLang($this->getForeignId(), 'pjPayment'));
			$this->set('i18n_options', pjMultiLangModel::factory()->getMultiLang($this->getForeignId(), 'pjOption'));
	
			$this->setLocalesData();
		}
	}
	
	public function pjActionNotifications()
	{
		if (!pjAuth::factory()->hasAccess())
		{
			$this->sendForbidden();
			return;
		}
		
		$arr = pjOptionModel::factory()
				->where('t1.foreign_id', $this->getForeignId())
				->where('t1.tab_id', 3)
				->orderBy('t1.order ASC')
				->findAll()
				->getData();
	
		$arr['i18n'] = pjMultiLangModel::factory()->getMultiLang($this->getForeignId(), 'pjOption');
	
		$this->set('arr', $arr);
	
		$this->setLocalesData();
	
		$this->appendCss('awesome-bootstrap-checkbox.css', PJ_THIRD_PARTY_PATH . 'awesome_bootstrap_checkbox/');
		$this->appendJs('jquery.multilang.js', $this->getConstant('pjBase', 'PLUGIN_JS_PATH'), false, false);
		$this->appendJs('tinymce.min.js', PJ_THIRD_PARTY_PATH . 'tinymce/');
		$this->appendJs('pjAdminOptions.js');
	}
	
	public function pjActionTerm()
	{
		if (!pjAuth::factory()->hasAccess())
		{
			$this->sendForbidden();
			return;
		}
		
		$arr = pjOptionModel::factory()
				->where('t1.foreign_id', $this->getForeignId())
				->where('t1.tab_id', 4)
				->orderBy('t1.order ASC')
				->findAll()
				->getData();
	
		$arr['i18n'] = pjMultiLangModel::factory()->getMultiLang($this->getForeignId(), 'pjCalendar');
	
		$this->set('arr', $arr);
	
		$this->setLocalesData();
	
		$this->appendJs('jquery.multilang.js', $this->getConstant('pjBase', 'PLUGIN_JS_PATH'), false, false);
		$this->appendJs('tinymce.min.js', PJ_THIRD_PARTY_PATH . 'tinymce/');
		$this->appendJs('pjAdminOptions.js');
	}
	
	public function pjActionReminder()
	{
		if (!pjAuth::factory()->hasAccess())
		{
			$this->sendForbidden();
			return;
		}
		
		$arr = pjOptionModel::factory()
				->where('t1.foreign_id', $this->getForeignId())
				->where('t1.tab_id', 4)
				->orderBy('t1.order ASC')
				->findAll()
				->getData();
	
		$arr['i18n'] = pjMultiLangModel::factory()->getMultiLang($this->getForeignId(), 'pjCalendar');
	
		$this->set('arr', $arr);
	
		$this->setLocalesData();
	
		$this->appendJs('jquery.multilang.js', $this->getConstant('pjBase', 'PLUGIN_JS_PATH'), false, false);
		$this->appendJs('tinymce.min.js', PJ_THIRD_PARTY_PATH . 'tinymce/');
		$this->appendJs('pjAdminOptions.js');
	}
	
	public function pjActionPreview()
	{
		$this->appendJs('pjAdminOptions.js');
	}
	
	public function pjActionUpdate()
	{
		$this->checkLogin();

		if (!pjAuth::factory()->hasAccess())
		{
			$this->sendForbidden();
			return;
		}
		
		if (self::isPost() && $this->_post->toInt('options_update'))
		{
			$pjOptionModel = new pjOptionModel();
			$pjOptionModel
				->where('foreign_id', $this->getForeignId())
				->where('type', 'bool')
				->where('tab_id', $this->_post->toInt('tab'))
				->modifyAll(array('value' => '1|0::0'));
			
			foreach ($this->_post->raw() as $key => $value)
			{
				if (preg_match('/value-(string|text|int|float|enum|bool|color)-(.*)/', $key) === 1)
				{
					list(, $type, $k) = explode("-", $key);
					if (!empty($k))
					{
						$_value = ':NULL';
						if ($value)
						{
							switch ($type)
							{
								case 'string':
								case 'text':
								case 'enum':
								case 'color':
									$_value = $this->_post->toString($key);
									break;
								case 'int':
								case 'bool':
									$_value = $this->_post->toInt($key);
									break;
								case 'float':
									$_value = $this->_post->toFloat($key);
									break;
							}
						}
			
						$pjOptionModel
						->reset()
						->where('foreign_id', $this->getForeignId())
						->where('`key`', $k)
						->limit(1)
						->modifyAll(array('value' => $_value));
					}
				}
			}
			
			$i18n_arr = $this->_post->toI18n('i18n');
			if (!empty($i18n_arr))
			{
				pjMultiLangModel::factory()->updateMultiLang($i18n_arr, $this->getForeignId(), 'pjCalendar', 'data');
			}
			
			if ($this->_post->check('tab'))
			{
				switch ($this->_post->toInt('tab'))
				{
					case '1':
						$err = 'AO01';
						break;
					case '2':
						$err = 'AO02';
						break;
					case '3':
						$err = 'AO04';
						break;
					case '4':
                        $is_reminder_enabled = $pjOptionModel
                            ->reset()
                            ->where('foreign_id', $this->getForeignId())
                            ->where('`key`', 'o_reminder_email_enable')
                            ->where('value', '1|0::1')
                            ->findCount()
                            ->getData();
                            
						$is_reminder_sms_enabled = $pjOptionModel
                            ->reset()
                            ->where('foreign_id', $this->getForeignId())
                            ->where('`key`', 'o_reminder_sms_enable')
                            ->where('value', '1|0::1')
                            ->findCount()
                            ->getData();
                            
						$active = (int) ($is_reminder_enabled || $is_reminder_sms_enabled);
                            
                        pjBaseCronJobModel::factory()->setIsActive($active, 'pjCron', 'pjActionIndex');
						$err = 'AO08';
						break;
					case '5':
						$err = 'AO06';
						break;
					case '7':
						$err = 'AO07';
						break;
				}
			}
			pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminOptions&action=" . $this->_post->toString('next_action') . "&err=$err");
		}
	}

	public function pjActionUpdateTheme()
	{
	$this->setAjax(true);
		
		if (!$this->isXHR())
		{
			self::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Missing headers.'));
		}
		
		if(!self::isPost())
		{
			self::jsonResponse(array('status' => 'ERR', 'code' => 101, 'text' => 'HTTP method not allowed.'));
		}
		
		if (!$this->_post->has('theme'))
		{
			self::jsonResponse(array('status' => 'ERR', 'code' => 102, 'text' => 'Missing, empty or invalid parameters.'));
		}
		
		pjOptionModel::factory()
			->where('foreign_id', $this->getForeignId())
			->where('`key`', 'o_theme')
			->limit(1)
			->modifyAll(array('value' => 'theme1|theme2|theme3|theme4|theme5|theme6|theme7|theme8|theme9|theme10::' . $this->_post->toString('theme')));
		
		self::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => 'Theme has been changed.'));
	}

	public function pjActionNotificationsGetMetaData()
	{
		$this->setAjax(true);
		
		if (!$this->isXHR())
		{
			self::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Missing headers.'));
		}
		
		if (!self::isGet())
		{
			self::jsonResponse(array('status' => 'ERR', 'code' => 101, 'text' => 'Invalid request.'));
		}
		
		if (!(isset($this->query['recipient']) && pjValidation::pjActionNotEmpty($this->query['recipient'])))
		{
			self::jsonResponse(array('status' => 'ERR', 'code' => 102, 'text' => 'Missing, empty or invalid parameters.'));
		}
		
		$this->set('arr', pjNotificationModel::factory()
			->where('t1.recipient', $this->query['recipient'])
			->orderBy('t1.id ASC')
			->findAll()
			->getData());
	}
	
	public function pjActionNotificationsGetContent()
	{
		$this->setAjax(true);
		
		if (!$this->isXHR())
		{
			self::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Missing headers.'));
		}
		
		if (!self::isGet())
		{
			self::jsonResponse(array('status' => 'ERR', 'code' => 101, 'text' => 'Invalid request.'));
		}
		
		if (!($this->_get->check('recipient') && $this->_get->check('variant') && $this->_get->check('transport'))
			&& pjValidation::pjActionNotEmpty($this->_get->toString('recipient'))
			&& pjValidation::pjActionNotEmpty($this->_get->toString('variant'))
			&& pjValidation::pjActionNotEmpty($this->_get->toString('transport'))
			&& in_array($this->_get->toString('transport'), array('email', 'sms'))
		)
		{
			self::jsonResponse(array('status' => 'ERR', 'code' => 102, 'text' => 'Missing, empty or invalid parameters.'));
		}
		
		$arr = pjNotificationModel::factory()
			->where('t1.recipient', $this->_get->toString('recipient'))
			->where('t1.variant', $this->_get->toString('variant'))
			->where('t1.transport', $this->_get->toString('transport'))
			->limit(1)
			->findAll()
			->getDataIndex(0);
		
		if (!$arr)
		{
			self::jsonResponse(array('status' => 'ERR', 'code' => 103, 'text' => 'Message not found.'));
		}
		
		$arr['i18n'] = pjBaseMultiLangModel::factory()->getMultiLang($this->getForeignId(), 'pjCalendar');
		$this->set('arr', $arr);
		
		# Check SMS
		$this->set('is_sms_ready', (isset($this->option_arr['plugin_sms_api_key']) && !empty($this->option_arr['plugin_sms_api_key']) ? 1 : 0));
		
		# Get locales
		$locale_arr = pjBaseLocaleModel::factory()
			->select('t1.*, t2.file, t2.title')
			->join('pjBaseLocaleLanguage', 't2.iso=t1.language_iso', 'left')
			->where('t2.file IS NOT NULL')
			->orderBy('t1.sort ASC')
			->findAll()
			->getData();
		
		$lp_arr = array();
		foreach ($locale_arr as $item)
		{
			$lp_arr[$item['id']."_"] = array($item['file'], $item['title']);
		}
		$this->set('lp_arr', $locale_arr);
		$this->set('locale_str', self::jsonEncode($lp_arr));
		$this->set('is_flag_ready', $this->requestAction(array('controller' => 'pjBaseLocale', 'action' => 'pjActionIsFlagReady'), array('return')));
	}
	
	public function pjActionNotificationsSetContent()
	{
		$this->setAjax(true);
		
		if (!$this->isXHR())
		{
			self::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Missing headers.'));
		}
		
		if (!self::isPost())
		{
			self::jsonResponse(array('status' => 'ERR', 'code' => 101, 'text' => 'Invalid request.'));
		}
		
		if (!(isset($this->body['id']) && pjValidation::pjActionNumeric($this->body['id'])))
		{
			self::jsonResponse(array('status' => 'ERR', 'code' => 102, 'text' => 'Missing, empty or invalid parameters.'));
		}
		
		$isToggle = $this->_post->check('is_active') && in_array($this->_post->toInt('is_active'), array(1,0));
		$isFormSubmit = $this->_post->check('i18n') && !$this->_post->isEmpty('i18n');
		
		if (!($isToggle xor $isFormSubmit))
		{
			self::jsonResponse(array('status' => 'ERR', 'code' => 103, 'text' => 'Data mismatch.'));
		}
		
		if ($isToggle)
		{
			pjNotificationModel::factory()
				->set('id', $this->_post->toInt('id'))
				->modify(array('is_active' => $this->_post->toInt('is_active')));
		} elseif ($isFormSubmit) {
			pjBaseMultiLangModel::factory()->updateMultiLang($this->_post->toArray('i18n'), $this->getForeignId(), 'pjCalendar');
		}
		
		self::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => 'Notification has been updated.'));
	}
}
?>
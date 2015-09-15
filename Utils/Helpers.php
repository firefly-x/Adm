<?php

namespace Adm\Utils;

class Helpers
{
	public static function sayidanYazi($sayi)
	{		
		$sayilarDizi = array_reverse(str_split($sayi));

		$rakamlar = [					
			0 => '',
			1 => 'Bir',
			2 => 'İki',
			3 => 'Üç',
			4 => 'Dört',
			5 => 'Beş',
			6 => 'Altı',
			7 => 'Yedi',
			8 => 'Sekiz',
			9 => 'Dokuz'
		];
		$onluklar = [
			0 => '',
			1 => 'On',
			2 => 'Yirmi',
			3 => 'Otuz',
			4 => 'Kırk',
			5 => 'Elli',
			6 => 'Altmış',
			7 => 'Yetmiş',
			8 => 'Seksen',
			9 => 'Doksan'
		];

		$rakamOkunus = [
			0 => function($value) use($rakamlar) {
				return $rakamlar[$value];
			},
			1 => function($value) use($onluklar) {
				return $onluklar[$value];
			},
			2 => function($value) use($rakamlar) {
				return ($value != 1) ? $rakamlar[$value] . 'yüz' : 'Yüz';
			}
		];
		$binlikHanesi = [
					0 => '',
					1 => 'bin',
					2 => 'milyon'
		];
		
		$chunked = array_chunk($sayilarDizi, 3);
		foreach ($chunked as $key => $sayiGrubu) {
			$result[] = $binlikHanesi[$key];
			foreach ($sayiGrubu as $rakamIdx => $rakam) {
				$result[] = $rakamOkunus[$rakamIdx]($rakam);
			}
		}
		return array_reverse($result);		
	}

	public static function stripPhoneNumber($telefon)
	{
		return str_replace(['(',')','-','+'], '', trim($telefon));
	}
}
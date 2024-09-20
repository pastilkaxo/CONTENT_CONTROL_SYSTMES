<?php

namespace FernleafSystems\Wordpress\Services\Utilities\WpOrg\Plugin;

use FernleafSystems\Wordpress\Services\Utilities\HttpUtil;
use FernleafSystems\Wordpress\Services\Utilities\URL;

class Download {

	use Base;

	/**
	 * @param string $version
	 */
	public function getDownloadUrlForVersion( $version ) :?string {
		$all = ( new Versions() )
			->setWorkingSlug( $this->getWorkingSlug() )
			->allVersionsUrls();
		return empty( $all[ $version ] ) ? null : URL::Build( $all[ $version ], [ 'nostats' => '1' ] );
	}

	/**
	 * @throws \Exception
	 */
	public function latest() :?string {
		$url = ( new Versions() )
			->setWorkingSlug( $this->getWorkingSlug() )
			->latest();
		return empty( $url ) ? null : ( new HttpUtil() )->downloadUrl( $url );
	}

	/**
	 * @param string $version
	 * @throws \Exception
	 */
	public function version( $version ) :?string {
		$url = $this->getDownloadUrlForVersion( $version );
		return empty( $url ) ? null : ( new HttpUtil() )->downloadUrl( $url );
	}
}
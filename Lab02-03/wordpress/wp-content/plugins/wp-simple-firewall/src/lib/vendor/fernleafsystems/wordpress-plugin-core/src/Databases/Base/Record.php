<?php declare( strict_types=1 );

namespace FernleafSystems\Wordpress\Plugin\Core\Databases\Base;

use FernleafSystems\Utilities\Data\Adapter\DynPropertiesClass;
use FernleafSystems\Wordpress\Plugin\Core\Databases\Common\HandlerConsumer;
use FernleafSystems\Wordpress\Plugin\Core\Databases\Common\Types;

/**
 * @property int   $id
 * @property array $meta
 * @property int   $created_at
 * @property int   $deleted_at
 */
class Record extends DynPropertiesClass {

	use HandlerConsumer;

	public function __construct( array $row = [] ) {
		$this->applyFromArray( $row );
	}

	public function applyFromArray( array $data, array $restrictedKeys = [] ) {

		if ( !empty( $this->getDbH() ) ) {
			$schema = $this->getDbH()->getTableSchema();
			foreach ( $data as $col => &$datum ) {
				if ( $datum !== null ) {
					$type = $schema->getColumnType( $col );
					if ( \in_array( $type, Types::Ints() ) ) {
						$datum = (int)$datum;
					}
					elseif ( $type === Types::MACROTYPE_BOOL ) {
						$datum = (bool)$datum;
					}
				}
			}
		}

		return parent::applyFromArray( $data, $restrictedKeys );
	}

	/**
	 * @return mixed
	 */
	public function __get( string $key ) {

		$value = parent::__get( $key );

		switch ( $key ) {

			case 'ip':
				if ( !empty( $value ) ) {
					$value = \inet_ntop( $value );
				}
				$value = (string)$value;
				break;

			case 'meta':
				$value = $this->arrayDataUnwrap( $value );
				if ( !\is_array( $value ) ) {
					$value = [];
				}
				break;

			default:
				break;
		}

		if ( $key === 'id' || \preg_match( '#^.*_at$#i', $key ) ) {
			$value = (int)$value;
		}

		return $value;
	}

	/**
	 * @param mixed $value
	 */
	public function __set( string $key, $value ) {

		switch ( $key ) {

			case 'ip':
				$value = \inet_pton( $value );
				if ( empty( $value ) ) {
					$value = '';
				}
				break;

			case 'meta':
				$value = $this->arrayDataWrap( $value );
				if ( !\is_string( $value ) ) {
					$value = '';
				}
				break;

			default:
				break;
		}

		parent::__set( $key, $value );
	}

	public function getHash() :string {
		$data = $this->getRawData();
		\asort( $data );
		return \md5( \serialize( $data ) );
	}

	public function isDeleted() :bool {
		return $this->deleted_at > 0;
	}

	public function arrayDataWrap( $data ) :?string {
		$wrapped = null;
		if ( \is_array( $data ) ) {
			$enc = \wp_json_encode( $data );
			if ( \is_string( $enc ) ) {
				$wrapped = \base64_encode( $enc );
			}
		}
		return $wrapped;
	}

	public function arrayDataUnwrap( $data ) :?array {
		$unwrapped = null;
		if ( \is_string( $data ) && !empty( $data ) ) {
			$data = \base64_decode( $data );
			if ( !empty( $data ) ) {
				$maybeUnwrapped = @\json_decode( $data, true );
				if ( \is_array( $maybeUnwrapped ) ) {
					$unwrapped = $maybeUnwrapped;
				}
			}
		}
		return $unwrapped;
	}
}
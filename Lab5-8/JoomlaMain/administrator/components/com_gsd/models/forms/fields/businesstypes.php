<?php

/**
 * @package         Google Structured Data
 * @version         5.6.5 Free
 *
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2021 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 */


defined('_JEXEC') or die;

use Joomla\CMS\Form\Field\ListField;
use Joomla\CMS\Language\Text;

class JFormFieldBusinessTypes extends ListField
{
	/**
	 *  Dropdown options array
	 *
	 *  @var  array
	 */
	private $options;

	/**
	 *  Schema.org Local Business Types
	 *
	 *  https://schema.org/docs/full.html#LocalBusiness
	 *
	 *  @var  array
	 */
	private $businessTypes = array(
		'AnimalShelter',
		'AutomotiveBusiness' => array(
			'AutoBodyShop',
			'AutoDealer',
			'AutoPartsStore',
			'AutoRental',
			'AutoRepair',
			'AutoWash',
			'GasStation',
			'MotorcycleDealer',
			'MotorcycleRepair'
		),
		'ChildCare',
		'DryCleaningOrLaundry',
		'EducationalOrganization' => [
			'CollegeOrUniversity',
			'ElementarySchool',
			'HighSchool',
			'MiddleSchool',
			'Preschool',
			'School'
		],
		'EmergencyService' => array(
			'FireStation',
			'Hospital',
			'PoliceStation'
		),
		'EmploymentAgency',
		'EntertainmentBusiness' => array(
			'AdultEntertainment',
			'AmusementPark',
			'ArtGallery',
			'Casino',
			'ComedyClub',
			'MovieTheater',
			'NightClub'
		),
		'FinancialService' => array(
			'AccountingService',
			'AutomatedTeller',
			'BankOrCreditUnion',
			'InsuranceAgency'
		),
		'FoodEstablishment' => [
			'Bakery',
			'BarOrPub',
			'Brewery',
			'CafeOrCoffeeShop',
			'FastFoodRestaurant',
			'IceCreamShop',
			'Restaurant',
			'Winery'
		],
		'GovernmentOffice' => array(
			'PostOffice'
		),
		'HealthAndBeautyBusiness' => array(
			'BeautySalon',
			'DaySpa',
			'HairSalon',
			'HealthClub',
			'NailSalon',
			'TattooParlor'
		),
		'HomeAndConstructionBusiness' => array(
			'Electrician',
			'GeneralContractor',
			'HVACBusiness',
			'HousePainter',
			'Locksmith',
			'MovingCompany',
			'Plumber',
			'RoofingContractor'
		),
		'InternetCafe',
		'LegalService' => array(
			'Attorney',
			'Notary'
		),
		'Library',
		'LocalBusiness',
		'LodgingBusiness'=> array(
			'BedAndBreakfast',
			'Campground',
			'Hostel',
			'Hotel',
			'Motel',
			'Resort'
		),
		'MedicalBusiness' => array(
			'Dentist',
			'Pharmacy',
			'Physician',
			'Optician'
		),
		'ProfessionalService',
		'RadioStation',
		'RealEstateAgent',
		'RecyclingCenter',
		'SelfStorage',
		'ShoppingCenter',
		'SportsActivityLocation' => array(
			'BowlingAlley',
			'ExerciseGym',
			'GolfCourse',
			'PublicSwimmingPool',
			'SkiResort',
			'SportsClub',
			'StadiumOrArena',
			'TennisComplex'
		),
		'Store' => array(
			'BikeStore',
			'BookStore',
			'ClothingStore',
			'ComputerStore',
			'ConvenienceStore',
			'DepartmentStore',
			'ElectronicsStore',
			'Florist',
			'FurnitureStore',
			'GardenStore',
			'GroceryStore',
			'HardwareStore',
			'HobbyShop',
			'HomeGoodsStore',
			'JewelryStore',
			'LiquorStore',
			'MedicalClinic',
			'MensClothingStore',
			'MobilePhoneStore',
			'MovieRentalStore',
			'MusicStore',
			'OfficeEquipmentStore',
			'OutletStore',
			'PawnShop',
			'PetStore',
			'ShoeStore',
			'SportingGoodsStore',
			'TireShop',
			'ToyStore',
			'WholesaleStore'
		),
		'TelevisionStation', 
		'TouristInformationCenter', 
		'TravelAgency'
	);

	/**
	 *  Returns all options to dropdown field
	 *
	 *  @return  array
	 */
	protected function getOptions()
	{
		return array_merge(parent::getOptions(), $this->buildTree($this->businessTypes));
	}

	/**
	 *  Recursive traversal of the businessTypes array tree
	 *
	 *  @param   Array    $types   The business types
	 *  @param   integer  $level   The array level
	 *
	 *  @return  array
	 */
	private function buildTree($types, $level = 0)
	{
		foreach ($types as $key => $type)
		{
			$hasChildren = is_array($type);
			$typeName    = $hasChildren ? $key : $type;
			$typeText    = str_repeat('- ', $level) . Text::_('GSD_BUSINESSLISTING_TYPE_' . $typeName);

			$this->options[] = array(
				'value'    => $typeName,
				'text'     => $typeText,
				'selected' => ($this->value == $typeName)
			);

			if ($hasChildren)
			{
				$this->buildTree($type, $level + 1);
			}
		}

		return $this->options;
	}
}
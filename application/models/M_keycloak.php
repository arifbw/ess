<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_keycloak extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
		$this->url_portal = @$this->config->item('PORTAL_URL'); //
		$this->url_keycloak = @$this->config->item('KEYCLOAK_URL');
		$this->realm = @$this->config->item('KEYCLOAK_REALM');
		$this->client_id = @$this->config->item('KEYCLOAK_CLIENT_ID');
		$this->admin_username = @$this->config->item('KEYCLOAK_ADMIN_USERNAME');
		$this->admin_password = @$this->config->item('KEYCLOAK_ADMIN_PASSWORD');
	}

	function master()
	{
		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => $this->url_keycloak . '/realms/' . $this->realm . '/protocol/openid-connect/token',
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'POST',
			CURLOPT_POSTFIELDS => 'client_id=admin-cli&username=' . $this->admin_username . '&password=' . $this->admin_password . '&grant_type=password',
			CURLOPT_HTTPHEADER => array(
				'Content-Type: application/x-www-form-urlencoded'
			),
			CURLOPT_SSL_VERIFYPEER => false, // Disable SSL certificate verification
			CURLOPT_SSL_VERIFYHOST => false, // Disable SSL hostname verification

		));
		$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		$response = curl_exec($curl);
		$out = json_decode($response, true);
		curl_close($curl);
		
		if ($out == null || !@$out['access_token']) return false;
		return $out['access_token'];
	}

	function auth($username, $password)
	{
		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => $this->url_keycloak . '/realms/' . $this->realm . '/protocol/openid-connect/token',
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'POST',
			CURLOPT_POSTFIELDS => 'client_id=' . $this->client_id . '&username=' . $username . '&password=' . $password . '&grant_type=password',
			CURLOPT_HTTPHEADER => array(
				'Content-Type: application/x-www-form-urlencoded'
			),
			CURLOPT_SSL_VERIFYPEER => false, // Disable SSL certificate verification
			CURLOPT_SSL_VERIFYHOST => false, // Disable SSL hostname verification

		));
		$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		$response = curl_exec($curl);
		$out = json_decode($response, true);
		curl_close($curl);
		return $out['access_token'];
	}

	function getUserByFirstName($firstName, $token = null)
	{
		if (!$token) $token = $this->master();
		
		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => $this->url_keycloak . '/admin/realms/' . $this->realm . '/users?firstName=' .  $firstName . '&enabled=true',
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'GET',
			CURLOPT_HTTPHEADER => array(
				'Content-Type: application/json',
				'Authorization: Bearer ' . $token
			),
			CURLOPT_SSL_VERIFYPEER => false, // Disable SSL certificate verification
			CURLOPT_SSL_VERIFYHOST => false, // Disable SSL hostname verification

		));
		$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		$response = curl_exec($curl);
		$out = json_decode($response, true);
		curl_close($curl);

		if (!empty($out)) return $out[0];
		return false;
	}

	function getRoleMapping($token, $id)
	{
		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => $this->url_keycloak . '/admin/realms/' . $this->realm . '/users/' . $id . '/role-mappings',
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'GET',
			CURLOPT_HTTPHEADER => array(
				'Authorization: Bearer ' . $token,
				'Content-Type: application/json'
			),
			CURLOPT_SSL_VERIFYPEER => false, // Disable SSL certificate verification
			CURLOPT_SSL_VERIFYHOST => false, // Disable SSL hostname verification

		));

		$response = curl_exec($curl);

		curl_close($curl);
		$out = json_decode($response, true);

		if (isset($out['clientMappings'][$this->client_id])) {
			return $out['clientMappings'][$this->client_id]['mappings'];
		} else {
			return [];
		}
	}

	function getUserId($token)
	{

		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => $this->url_keycloak . '/realms/' . $this->realm . '/account',
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'GET',
			CURLOPT_HTTPHEADER => array(
				'Authorization: Bearer ' . $token,
				'Content-Type: application/json'
			),
			CURLOPT_SSL_VERIFYPEER => false, // Disable SSL certificate verification
			CURLOPT_SSL_VERIFYHOST => false, // Disable SSL hostname verification
		));

		$response = curl_exec($curl);
		$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		curl_close($curl);


		$out = json_decode($response);
		if (!empty($out->error)) return false;
		return $out;
	}

	function getUnitKerja($token, $id)
	{

		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => $this->url_keycloak . '/admin/realms/' . $this->realm . '/users/' . $id . '/groups',
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'GET',
			CURLOPT_HTTPHEADER => array(
				'Authorization: Bearer ' . $token,
				'Content-Type: application/json'
			),
			CURLOPT_SSL_VERIFYPEER => false, // Disable SSL certificate verification
			CURLOPT_SSL_VERIFYHOST => false, // Disable SSL hostname verification
		));

		$response = curl_exec($curl);

		curl_close($curl);

		$out = json_decode($response);
		return $out;
	}

	function matchRole($existing_data, $desired_id_grup_pengguna, $id_pengguna)
	{
		$tabel = 'usr_pengguna_grup_pengguna';
		$matchedIds = array(); // Untuk menyimpan id yang cocok
		$notMatchedIds = array(); // Untuk menyimpan id yang tidak cocok

		foreach ($desired_id_grup_pengguna as $desired_item) {
			$desired_id = $desired_item['description'];
			$matched = false;

			foreach ($existing_data as $row) {
				$id_grup = $row['id'];

				if ($desired_id == $id_grup) {
					// Jika ditemukan, tambahkan ke dalam array $matchedIds
					$matchedIds[] = array(
						'id' => $id_grup,
						'nama' => $desired_item['name']
					);
					$matched = true;
					break;
				}
			}

			if (!$matched) {
				// Jika tidak cocok, tambahkan ke dalam array $notMatchedIds
				$notMatchedIds[] = array(
					'id' => $desired_id,
					'nama' => $desired_item['name']
				);

				// Langkah 5: Tambahkan data yang belum ada ke database
				$data_insert = array(
					'id_grup_pengguna' => $desired_id,
					'id_pengguna' => $id_pengguna,
					// Tambahkan kolom lain sesuai kebutuhan
				);
				$this->db->where('id_pengguna', $id_pengguna);
				$this->db->insert($tabel, $data_insert); // Ganti dengan nama tabel Anda
			}
		}

		// Langkah 4: Hapus data yang tidak sesuai dengan $desired_id_grup_pengguna
		$existing_ids = array_column($existing_data, 'id');
		$ids_to_delete = array_diff($existing_ids, array_column($desired_id_grup_pengguna, 'description'));

		if (!empty($ids_to_delete)) {

			$this->db->where('id_pengguna', $id_pengguna);
			$this->db->where_in('id_grup_pengguna', $ids_to_delete);
			$this->db->delete($tabel); // Ganti dengan nama tabel Anda
		}

		return $this->m_login->ambil_grup($id_pengguna);
	}

	function matchUnitKerja($existing_data, $unit_kerja, $id_pengguna)
	{

		$tabel = 'usr_pengadministrasi';
		$matchedIds = array(); // Untuk menyimpan id yang cocok
		$notMatchedIds = array(); // Untuk menyimpan id yang tidak cocok

		foreach ($unit_kerja as $desired_item) {
			$desired_id = $desired_item->kode_unit;

			$matched = false;

			foreach ($existing_data as $row) {
				$id_grup = $row['kode_unit'];

				if ($desired_id == $id_grup) {
					// Jika ditemukan, tambahkan ke dalam array $matchedIds
					$matchedIds[] = array(
						'id_pengguna' => $id_pengguna,
						'kode_unit' => $desired_item->kode_unit
					);
					$matched = true;
					break;
				}
			}

			if (!$matched) {
				// Jika tidak cocok, tambahkan ke dalam array $notMatchedIds
				$notMatchedIds[] = array(
					'id_pengguna' => $desired_id,
					'kode_unit' => $desired_item->kode_unit
				);

				// Langkah 5: Tambahkan data yang belum ada ke database
				$data_insert = array(
					'id_pengguna' => $id_pengguna,
					'kode_unit' => $desired_id,
					// Tambahkan kolom lain sesuai kebutuhan
				);

				$this->db->where('id_pengguna', $id_pengguna);
				$this->db->insert($tabel, $data_insert); // Ganti dengan nama tabel Anda
			}
		}
		// Langkah 4: Hapus data yang tidak sesuai dengan $unit_kerja
		$existing_ids = array_column($existing_data, 'kode_unit');

		//ubah list object menjadi list array
		$array_unit_kerja = array_map(function ($obj) {
			return (array) $obj;
		}, $unit_kerja);
		
		$unit_kerja_ids = array_column($array_unit_kerja, 'kode_unit');
		$ids_to_delete = array_diff($existing_ids, $unit_kerja_ids);

		if (!empty($ids_to_delete)) {

			$this->db->where('id_pengguna', $id_pengguna);
			$this->db->where_in('kode_unit', $ids_to_delete);
			$this->db->delete($tabel); // Ganti dengan nama tabel Anda
		}

		return $this->m_login->list_pengadministrasi($id_pengguna);
	}

	function refresh_token($refresh_token)
	{
		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => $this->url_keycloak . '/realms/' . $this->realm . '/protocol/openid-connect/token',
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'POST',
			CURLOPT_POSTFIELDS => "client_id=admin-cli&grant_type=refresh_token&refresh_token=$refresh_token",
			CURLOPT_HTTPHEADER => array(
				'Content-Type: application/x-www-form-urlencoded'
			),
			CURLOPT_SSL_VERIFYPEER => false, // Disable SSL certificate verification
			CURLOPT_SSL_VERIFYHOST => false, // Disable SSL hostname verification
		));

		$response_refresh = curl_exec($curl);
		$arr_res = json_decode($response_refresh);
		$httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		curl_close($curl);

		return $arr_res;
	}

	function getRealm($token)
	{
		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => $this->url_keycloak . '/admin/realms/' . $this->realm . '/clients',
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'GET',
			CURLOPT_HTTPHEADER => array(
				'Content-Type: application/json',
				'Authorization: Bearer ' . $token
			),
			CURLOPT_SSL_VERIFYPEER => false, // Disable SSL certificate verification
			CURLOPT_SSL_VERIFYHOST => false, // Disable SSL hostname verification
		));

		$response = curl_exec($curl);
		$httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		curl_close($curl);

		if ($httpcode != 200) return false;

		$arr_res = json_decode($response, true);
		$clientId = null;
		foreach ($arr_res as $client) {
			if ($client['clientId'] == $this->client_id) {
				$clientId = $client['id'];
				break;
			}
		}
		return $clientId;
	}

	function getRoleClient($token = null, $clientId = null)
	{

		if (!$token)  $token = $this->master();

		if (!$clientId) $clientId =	$this->getRealm($token);

		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => $this->url_keycloak . '/admin/realms/' . $this->realm . '/clients/' . $clientId . '/roles',
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'GET',
			CURLOPT_HTTPHEADER => array(
				'Content-Type: application/json',
				'Authorization: Bearer ' . $token
			),
			CURLOPT_SSL_VERIFYPEER => false, // Disable SSL certificate verification
			CURLOPT_SSL_VERIFYHOST => false, // Disable SSL hostname verification
		));

		$response = curl_exec($curl);
		$httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

		curl_close($curl);

		if ($httpcode != 200) return false;
		$arr_res = json_decode($response, true);
		return $arr_res;
	}

	function assignRole($grup, $np, $token = null, $clientId = null)
	{

		if (!$token)  $token = $this->master();

		$user = $this->getUserByFirstName($np, $token);
		if (!$user) {
			return 'User not found!';
		}
		$userId = $user['id'];

		if (!$clientId) $clientId =	$this->getRealm($token);


		$roleClient = $this->getRoleClient($token);

		$idsRoleClient = [];

		foreach ($roleClient as $role) {
			$id = $role['id'];
			$name = $role['name'];
			$description = $role['description'];

			$idsRoleClient[] = [
				'id' => $id,
				'name' => $name,
				'description' => $description,
			];
		}

		$filteredRoles = array_filter($idsRoleClient, function ($role) use ($grup) {
			return in_array($role['description'], $grup);
		});

		$filteredRolesArray = array_values($filteredRoles);

		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => $this->url_keycloak . '/admin/realms/' . $this->realm . '/users/' . $userId . '/role-mappings/clients/' . $clientId,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'POST',
			CURLOPT_POSTFIELDS => json_encode($filteredRolesArray),
			CURLOPT_HTTPHEADER => array(
				'Content-Type: application/json',
				'Authorization: Bearer ' . $token
			),
			CURLOPT_SSL_VERIFYPEER => false, // Disable SSL certificate verification
			CURLOPT_SSL_VERIFYHOST => false, // Disable SSL hostname verification
		));

		$response = curl_exec($curl);
		$httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		curl_close($curl);

		if ($httpcode != 204) return false;

		return true;
	}

	function deleteRole($np, $token = null, $clientId = null)
	{

		if (!$token) $token = $this->master();

		$user = $this->getUserByFirstName($np, $token);
		if (!$user) {
			return 'User not found!';
		}
		$userId = $user['id'];

		if (!$clientId) $clientId =	$this->getRealm($token);

		$roles = $this->getRoleMapping($token, $userId);
		$idsRole = [];

		foreach ($roles as $role) {
			$id = $role['id'];
			$name = $role['name'];

			$idsRole[] = [
				'id' => $id,
				'name' => $name,
			];
		}

		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => $this->url_keycloak . '/admin/realms/' . $this->realm . '/users/' . $userId . '/role-mappings/clients/' . $clientId,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'DELETE',
			CURLOPT_POSTFIELDS => json_encode($idsRole),
			CURLOPT_HTTPHEADER => array(
				'Content-Type: application/json',
				'Authorization: Bearer ' . $token
			),
			CURLOPT_SSL_VERIFYPEER => false, // Disable SSL certificate verification
			CURLOPT_SSL_VERIFYHOST => false, // Disable SSL hostname verification
		));

		$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		$response = curl_exec($curl);

		curl_close($curl);

		if ($httpCode != 204) return false;

		return true;
	}

	function getRoleByUser($np, $token = null)
	{

		if (!$token) $token = $this->master();

		$user = $this->getUserByFirstName($np, $token);
		if (!$user) {
			return 'User not found!';
		}
		$userId = $user['id'];

		$userRole = $this->getRoleMapping($token, $userId);

		if (empty($userRole)) return false;

		return [
			'id' => $userId,
			'role' => $userRole,
		];
	}

	function createOtoritas($role, $token = null)
	{

		if (!$token) $token = $this->master();

		$clientId = $this->getRealm($token);
		if (!$clientId) return 'Client not found!';


		$idsRole = [
			"name" => $role['nama'],
			"composite" => false,
			"clientRole" => true,
			"description" => $role['id'],
		];

		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => $this->url_keycloak . '/admin/realms/' . $this->realm . '/clients/' . $clientId . '/roles',
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'POST',
			CURLOPT_POSTFIELDS => json_encode($idsRole),
			CURLOPT_HTTPHEADER => array(
				'Content-Type: application/json',
				'Authorization: Bearer ' . $token
			),
			CURLOPT_SSL_VERIFYPEER => false, // Disable SSL certificate verification
			CURLOPT_SSL_VERIFYHOST => false, // Disable SSL hostname verification
		));

		$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		$response = curl_exec($curl);

		curl_close($curl);
		return $response;
	}

	function editOtoritas($role, $token = null)
	{

		if (!$token) $token = $this->master();

		$clientId = $this->getRealm($token);
		if (!$clientId) {
			return 'Client not found!';
		}
		$roleClient = $this->getRoleClient($token);

		$otoritas = [$role['data']['id']];

		$filteredRoles = array_values(array_filter($roleClient, function ($roles) use ($otoritas) {
			return in_array($roles['description'], $otoritas);
		}));

		$foundItems = array();

		foreach ($roleClient as $item) {
			if (isset($item['name']) && $item['name'] === $role['data']['nama']) {
				$foundItems[] = $item;
			}
		}

		if (!empty($filteredRoles) && !empty($foundItems) && !$role['set']['status']) {
			$curl = curl_init();

			curl_setopt_array($curl, array(
				CURLOPT_URL => $this->url_keycloak . '/admin/realms/' . $this->realm . '/roles-by-id/' . $filteredRoles[0]['id'],
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => '',
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 0,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => 'DELETE',
				CURLOPT_HTTPHEADER => array(
					'Content-Type: application/json',
					'Authorization: Bearer ' . $token
				),
				CURLOPT_SSL_VERIFYPEER => false, // Hanya jika Anda ingin melewatkan verifikasi SSL (hati-hati dalam lingkungan produksi)
				CURLOPT_SSL_VERIFYHOST => false // Hanya jika Anda ingin melewatkan verifikasi host SSL
			));

			$response = curl_exec($curl);
			$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

			curl_close($curl);

			if ($httpCode == 204) {
				// echo "Delete request successful. Status Code: " . $httpCode;
				return true;
			} else {
				// echo "Failed to perform the DELETE request. Status Code: " . $httpCode;
				return false;
			}
		} else if (!empty($filteredRoles) && !empty($foundItems)) {

			$curl = curl_init();

			$data = [
				'name' => $role['set']['nama'],
				"description" => $role['data']['id'],
			];

			curl_setopt_array($curl, array(
				CURLOPT_URL => $this->url_keycloak . '/admin/realms/' . $this->realm . '/clients/' . $clientId . '/roles/' . rawurlencode($role['data']['nama']),
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => '',
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 0,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => 'PUT',
				CURLOPT_POSTFIELDS => json_encode($data),
				CURLOPT_HTTPHEADER => array(
					'Content-Type: application/json',
					'Authorization: Bearer ' . $token
				),
				CURLOPT_SSL_VERIFYPEER => false, // Hanya jika Anda ingin melewatkan verifikasi SSL (hati-hati dalam lingkungan produksi)
				CURLOPT_SSL_VERIFYHOST => false // Hanya jika Anda ingin melewatkan verifikasi host SSL
			));

			$response = curl_exec($curl);
			$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
			$error = curl_error($curl);

			curl_close($curl);

			if ($httpCode == 204) {
				// echo "Delete request successful. Status Code: " . $httpCode;
				return true;
			} else {
				// echo "Failed to perform the DELETE request. Status Code: " . $httpCode;
				return false;
			}
		} else if ($role['data']['id'] && empty($filteredRoles) && empty($foundItems) && $role['set']['status']) {

			$this->createOtoritas($role['data'], $token);
		}
	}

	function syncOtoritasKeycloak($data, $token = null)
	{
		if (!$token) $token = $this->master();

		$clientId = $this->getRealm($token);

		if (!$clientId) {
			return 'Client not found!';
		}

		$roleClient = $this->getRoleClient($token);

		$dataIds = [];

		foreach ($data as  $value) {
			$dataIds[] = $value['id'];
		}

		$filteredRoles = array_values(array_filter($roleClient, function ($roles) use ($dataIds) {
			return in_array($roles['description'], $dataIds);
		}));

		$differences = [];

		foreach ($dataIds as $role) {
			$found = false;
			foreach ($roleClient as $data) {
				if ($role === $data['description']) {
					$found = true;
					break;
				}
			}
			if (!$found) {
				$differences[] = $role;
			}
		}

		// print_r(json_encode($roleClient));
		// die;
	}

	function syncKodeUnit($np, $token = null)
	{
		$dataUser  = $this->db->select('id,username,no_pokok')->where('no_pokok', $np)->get('usr_pengguna')->row_array();

		if (!$token) $token = $this->master();

		$user = $this->getUserByFirstName($np, $token);
		if (!$user) {
			return 'User not found!';
		}
		$userId = $user['id'];

		$unit_kerja = $this->getUnitKerja($token, $userId);

		foreach ($unit_kerja as $item) {
			$nameParts = explode('_', $item->name);
			$item->name = $nameParts[1];
			$item->kode_unit = $nameParts[0];
		}
		print_r(json_encode($unit_kerja));
		die;
		$list_pengadministrasi	= $this->m_login->list_pengadministrasi($dataUser['id']);
	}

	function getSubGroup($token = null)
	{
		if (!$token) $token = $this->master();

		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => $this->url_keycloak . '/admin/realms/' . $this->realm . '/groups',
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'GET',
			CURLOPT_HTTPHEADER => array(
				'Content-Type: application/json',
				'Authorization: Bearer ' . $token
			),
			CURLOPT_SSL_VERIFYPEER => false, // Disable SSL certificate verification
			CURLOPT_SSL_VERIFYHOST => false, // Disable SSL hostname verification

		));
		$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		$response = curl_exec($curl);
		$out = json_decode($response, true);
		curl_close($curl);

		$subGroup = [];
		foreach ($out as $item) {
			if ($item['name'] === 'Kode Unit') {
				$subGroup = $item['subGroups'];
			}
		}

		$subGroupIds = [];

		foreach ($subGroup as $item) {
			$nameParts = explode('_', $item['name']);
			$subGroupIds[] = [
				'id' => $item['id'],
				'name' => $nameParts[1],
				'kode_unit' => $nameParts[0],
			];
		}

		if (!empty($out)) return $subGroupIds;
		return false;
	}

	function assignKodeUnit($np, $data, $token = null)
	{

		if (!$token) $token = $this->master();

		$user = $this->getUserByFirstName($np, $token);
		if (!$user) {
			return 'User not found!';
		}
		$userId = $user['id'];

		$subGroup = $this->getSubGroup($token);

		$filteredRoles = array_values(array_filter($subGroup, function ($roles) use ($data) {
			return in_array($roles['kode_unit'], $data);
		}));

		$result = [];

		foreach ($filteredRoles as  $item) {

			$curl = curl_init();

			curl_setopt_array($curl, array(
				CURLOPT_URL => $this->url_keycloak . '/admin/realms/' . $this->realm . '/users/' . $userId . '/groups/' . $item['id'],
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => '',
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 0,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => 'PUT',
				CURLOPT_HTTPHEADER => array(
					'Content-Type: application/json',
					'Authorization: Bearer ' . $token
				),
				CURLOPT_SSL_VERIFYPEER => false, // Hanya jika Anda ingin melewatkan verifikasi SSL (hati-hati dalam lingkungan produksi)
				CURLOPT_SSL_VERIFYHOST => false // Hanya jika Anda ingin melewatkan verifikasi host SSL
			));

			$response = curl_exec($curl);
			$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
			$error = curl_error($curl);

			curl_close($curl);

			if ($httpCode == 204) {
				$result[] = [
					'status' => true,
					'id' => $item['id'],
					'name' => $item['kode_unit']
				];
			} else {
				$result[] = [
					'status' => false,
					'id' => $item['id'],
					'name' => $item['kode_unit']
				];
				// echo "Failed to perform the DELETE request. Status Code: " . $httpCode;
			}
		}

		return $result;
	}

	function unassignKodeUnit($np, $data, $token = null)
	{

		if (!$token) $token = $this->master();

		$user = $this->getUserByFirstName($np, $token);
		if (!$user) {
			return 'User not found!';
		}
		$userId = $user['id'];

		$subGroup = $this->getSubGroup($token);

		$filteredRoles = array_values(array_filter($subGroup, function ($roles) use ($data) {
			return in_array($roles['kode_unit'], $data);
		}));

		$result = [];


		foreach ($filteredRoles as  $item) {
			$curl = curl_init();

			curl_setopt_array($curl, array(
				CURLOPT_URL => $this->url_keycloak . '/admin/realms/' . $this->realm . '/users/' . $userId . '/groups/' . $item['id'],
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => '',
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 0,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => 'DELETE',
				CURLOPT_HTTPHEADER => array(
					'Content-Type: application/json',
					'Authorization: Bearer ' . $token
				),
				CURLOPT_SSL_VERIFYPEER => false, // Hanya jika Anda ingin melewatkan verifikasi SSL (hati-hati dalam lingkungan produksi)
				CURLOPT_SSL_VERIFYHOST => false // Hanya jika Anda ingin melewatkan verifikasi host SSL
			));

			$response = curl_exec($curl);
			$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
			$error = curl_error($curl);

			curl_close($curl);

			if ($httpCode == 204) {
				$result[] = [
					'status' => true,
					'id' => $item['id'],
					'name' => $item['kode_unit']
				];
			} else {
				$result[] = [
					'status' => false,
					'id' => $item['id'],
					'name' => $item['kode_unit']
				];
				// echo "Failed to perform the DELETE request. Status Code: " . $httpCode;
			}
		}
	}

	function getPassword($np)
	{
		$curl = curl_init();

		curl_setopt_array($curl, array(
			// CURLOPT_URL => $this->url_keycloak . '/realms/' . $this->realm . '/protocol/openid-connect/token',
			CURLOPT_URL => $this->url_portal . '/user/getpassword?no_pokok=' . $np,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'GET',
			CURLOPT_HTTPHEADER => array(
				'Content-Type: application/json',
			),
			CURLOPT_SSL_VERIFYPEER => false, // Disable SSL certificate verification
			CURLOPT_SSL_VERIFYHOST => false, // Disable SSL hostname verification
		));

		$response_refresh = curl_exec($curl);
		$arr_res = json_decode($response_refresh);
		$httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		curl_close($curl);
		if (@$arr_res->status) return $arr_res->data;
		return false;
	}

	protected $pass_key = 'KeycloakSSO2023';

	public function encrypt($data)
	{
		$key = $this->pass_key;
		$ivSize = openssl_cipher_iv_length('AES-256-CBC');
		$iv = openssl_random_pseudo_bytes($ivSize);
		$encrypted = openssl_encrypt($data, 'AES-256-CBC', $key, 0, $iv);
		return base64_encode($iv . $encrypted);
	}

	public function decrypt($data)
	{
		$key = $this->pass_key;
		$data = base64_decode($data);
		$ivSize = openssl_cipher_iv_length('AES-256-CBC');
		$iv = substr($data, 0, $ivSize);
		$data = substr($data, $ivSize);
		$decrypted = openssl_decrypt($data, 'AES-256-CBC', $key, 0, $iv);
		return $decrypted;
	}
}

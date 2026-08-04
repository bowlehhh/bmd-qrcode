<?php

namespace Tests\Feature;

use App\Models\Asset;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use ZipArchive;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_root_redirects_to_login(): void
    {
        $response = $this->get('/');

        $response->assertRedirect('/login');
    }

    public function test_authenticated_user_can_open_dashboard(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
        ]);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertOk();
        $response->assertSee('Ringkasan Aset BMD');
    }

    public function test_public_asset_detail_can_be_opened_from_qr_url(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
        ]);

        $asset = Asset::create([
            'asset_code' => 'BMD-00125',
            'name' => 'Laptop Lenovo',
            'category' => 'Elektronik',
            'brand' => 'Lenovo',
            'year_acquired' => 2024,
            'location' => 'Ruang IT',
            'condition' => 'baik',
            'qr_code_path' => 'assets/qrcodes/BMD-00125.svg',
            'qr_target_url' => '/aset/BMD-00125',
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        $response = $this->get(route('assets.public.show', $asset));

        $response->assertOk();
        $response->assertSee('Laptop Lenovo');
        $response->assertSee('BMD-00125');
    }

    public function test_non_admin_cannot_open_asset_create_page(): void
    {
        $user = User::factory()->create([
            'role' => 'viewer',
        ]);

        $response = $this->actingAs($user)->get(route('assets.create'));

        $response->assertForbidden();
    }

    public function test_admin_can_fetch_asset_selection_without_loading_full_dataset(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
        ]);

        Asset::factory()->count(25)->create([
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        $response = $this->actingAs($user)->getJson(route('assets.selection', ['q' => '']));

        $response->assertOk();
        $response->assertJsonPath('per_page', 20);
        $response->assertJsonCount(20, 'data');
    }

    public function test_bulk_word_export_downloads_a_zip_with_a_document_for_each_asset(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
        ]);

        $firstAsset = Asset::factory()->create([
            'asset_code' => 'BMD-10001',
            'name' => 'Laptop Bidang Umum',
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);
        $secondAsset = Asset::factory()->create([
            'asset_code' => 'BMD-10002',
            'name' => 'Printer Kantor',
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        $response = $this->actingAs($user)->post(route('assets.export.word.bulk'), [
            'asset_ids' => [$firstAsset->id, $secondAsset->id],
        ]);

        $response->assertOk();
        $response->assertHeader('content-disposition');
        $this->assertStringContainsString('kodebarang-', (string) $response->headers->get('content-disposition'));

        $archivePath = $response->baseResponse->getFile()->getPathname();
        $archive = new ZipArchive();

        $this->assertTrue($archive->open($archivePath) === true);
        $this->assertNotFalse($archive->locateName('kodebarang/Laptop Bidang Umum - BMD-10001.docx'));
        $this->assertNotFalse($archive->locateName('kodebarang/Printer Kantor - BMD-10002.docx'));
        $archive->close();

        @unlink($archivePath);
    }
}

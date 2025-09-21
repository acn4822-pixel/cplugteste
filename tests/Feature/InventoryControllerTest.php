<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Inventory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InventoryControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Teste para um lançamento de inventário com sucesso.
     */
    public function test_inventory_can_be_stored_successfully()
    {
        // 1. Configurar o ambiente (cenário de teste)
        $product = Product::factory()->create([
            'sku' => 'TEST-SKU-1',
        ]);
        
        $data = [
            'sku' => 'TEST-SKU-1',
            'quantity' => 5,
        ];

        // 2. Executar a ação (chamar a API)
        $response = $this->postJson('/api/inventory', $data);

        // 3. Verificar o resultado (asserções)
        $response->assertStatus(201)
                 ->assertJson([
                     'message' => 'Entrada de produto registrada com sucesso.',
                     'data' => [
                         'sku' => 'TEST-SKU-1',
                         'quantity' => 5,
                     ],
                 ]);
        
        // Verifica se o registro foi criado no banco de dados
        $this->assertDatabaseHas('inventories', [
            'sku' => 'TEST-SKU-1',
            'quantity' => 5,
            'product_id' => $product->id,
        ]);
    }

    /**
     * Teste para verificar a falha na validação.
     */
    public function test_inventory_creation_fails_with_invalid_data()
    {
        // Envia dados incompletos
        $data = [
            'sku' => 'TEST-SKU-2',
            // 'quantity' está faltando
        ];

        $response = $this->postJson('/api/inventory', $data);

        // Verifica o status 422 e a mensagem de erro
        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['quantity']);
    }

    /**
     * Teste para verificar a falha quando o produto não existe.
     */
    public function test_inventory_creation_fails_if_product_not_found()
    {
        // Envia um SKU que não existe no banco de dados
        $data = [
            'sku' => 'NON-EXISTENT-SKU',
            'quantity' => 10,
        ];

        $response = $this->postJson('/api/inventory', $data);

        // Verifica o status 404 e a mensagem de erro
        $response->assertStatus(404)
                 ->assertJson([
                     'message' => 'O produto com este SKU não existe.'
                 ]);
        
        // Garante que nenhum registro foi criado no banco de dados
        $this->assertDatabaseMissing('inventories', [
            'sku' => 'NON-EXISTENT-SKU'
        ]);
    }
}
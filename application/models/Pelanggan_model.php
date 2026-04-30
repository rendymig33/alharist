<?php
declare(strict_types=1);

class Pelanggan_model extends Model
{
    public function nextCode(): string
    {
        $lastCode = (string) $this->db->query("SELECT code FROM customers WHERE code LIKE 'PLG%' ORDER BY id DESC LIMIT 1")->fetchColumn();
        $lastNumber = 0;

        if ($lastCode !== '' && preg_match('/PLG(\d+)/', $lastCode, $matches)) {
            $lastNumber = (int) $matches[1];
        }

        return 'PLG' . str_pad((string) ($lastNumber + 1), 4, '0', STR_PAD_LEFT);
    }

    public function all(): array
    {
        return $this->db->query("SELECT * FROM customers ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
    }

    public function search(string $keyword = ''): array
    {
        $keyword = trim($keyword);
        if ($keyword === '') {
            return $this->all();
        }

        $statement = $this->db->prepare("
            SELECT *
            FROM customers
            WHERE code LIKE :keyword
               OR name LIKE :keyword
               OR phone LIKE :keyword
               OR address LIKE :keyword
            ORDER BY id DESC
        ");
        $statement->execute(['keyword' => '%' . $keyword . '%']);
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function save(array $data): void
    {
        if (empty($data['code'])) {
            $data['code'] = $this->nextCode();
        }

        if (empty($data['id'])) {
            $existing = $this->findByCode((string) $data['code']);
            if ($existing) {
                $data['id'] = $existing['id'];
            }
        }

        if (!empty($data['id'])) {
            $sql = "UPDATE customers SET code=:code, name=:name, phone=:phone, address=:address WHERE id=:id";
            $params = [
                'id' => $data['id'],
                'code' => $data['code'],
                'name' => $data['name'],
                'phone' => $data['phone'],
                'address' => $data['address'],
            ];
        } else {
            $sql = "INSERT INTO customers (code, name, phone, address, created_at) VALUES (:code, :name, :phone, :address, :created_at)";
            $params = [
                'code' => $data['code'],
                'name' => $data['name'],
                'phone' => $data['phone'],
                'address' => $data['address'],
                'created_at' => date('Y-m-d H:i:s'),
            ];
        }

        $statement = $this->db->prepare($sql);
        $statement->execute($params);
    }

    public function delete(int $id): bool
    {
        $statement = $this->db->prepare("DELETE FROM customers WHERE id = :id");
        return $statement->execute(['id' => $id]);
    }

    public function findByCode(string $code): array|false
    {
        $statement = $this->db->prepare("SELECT * FROM customers WHERE code = :code");
        $statement->execute(['code' => $code]);
        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    public function importCsv(string $filePath): int
    {
        $handle = fopen($filePath, 'r');
        if ($handle === false) {
            return 0;
        }

        $count = 0;
        fgetcsv($handle);
        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) < 4) {
                continue;
            }

            $this->save([
                'code' => trim((string) $row[0]),
                'name' => trim((string) $row[1]),
                'phone' => trim((string) $row[2]),
                'address' => trim((string) $row[3]),
            ]);
            $count++;
        }

        fclose($handle);
        return $count;
    }
}

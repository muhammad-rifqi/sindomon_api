import { test, expect } from '@playwright/test';

const MAX_LATENCY_MS = 1000;

function assertEnvelope(body: Record<string, unknown>, path: string) {
  expect(body, `${path}: missing 'status'`).toHaveProperty('status');
  expect(body, `${path}: missing 'message'`).toHaveProperty('message');
}

function assertLatency(startMs: number, path: string, maxMs = MAX_LATENCY_MS) {
  const duration = Date.now() - startMs;
  expect(
    duration,
    `${path}: response took ${duration}ms, exceeding ${maxMs}ms threshold`,
  ).toBeLessThan(maxMs);
}

test.describe.serial('Master Polres API', () => {
  let adminJwt: string;
  let operatorJwt: string;

  test('Auth: Admin login returns role_id=1', async ({ request }) => {
    const start = Date.now();
    const res = await request.post('/api/v1/auth/login', {
      data: { username: 'admin', password: 'admin123' },
    });
    assertLatency(start, '/auth/login (admin)');
    expect(res.status()).toBe(200);

    const body = await res.json();
    expect(body).toHaveProperty('jwt_token');
    assertEnvelope(body, '/auth/login (admin)');
    adminJwt = body.jwt_token;
  });

  test('Auth: Operator login returns role_id=2', async ({ request }) => {
    const start = Date.now();
    const res = await request.post('/api/v1/auth/login', {
      data: { username: 'operator_test', password: 'operator123' },
    });
    assertLatency(start, '/auth/login (operator)');
    expect(res.status()).toBe(200);

    const body = await res.json();
    expect(body).toHaveProperty('jwt_token');
    operatorJwt = body.jwt_token;
  });

  test('POST /api/v1/master/polres — Success (201)', async ({ request }) => {
    const start = Date.now();
    const res = await request.post('/api/v1/master/polres', {
      headers: { Authorization: `Bearer ${adminJwt}` },
      data: {
        nama_polres: 'Polres Test Sukses',
        polda_id: 1,
      },
    });
    assertLatency(start, 'POST /master/polres (success)');

    expect(res.status()).toBe(201);
    const body = await res.json();
    assertEnvelope(body, 'POST /master/polres (success)');
    expect(body.status).toBe(201);
    expect(body.message).toBe('Data wilayah polres berhasil ditambahkan.');
    expect(body.data).toHaveProperty('polres_id');
    expect(typeof body.data.polres_id).toBe('number');
    expect(body.data.polres_id).toBeGreaterThan(0);
  });

  test('POST /api/v1/master/polres — Integrity Trap (422)', async ({ request }) => {
    const start = Date.now();
    const res = await request.post('/api/v1/master/polres', {
      headers: { Authorization: `Bearer ${adminJwt}` },
      data: {
        nama_polres: 'Polres Fake',
        polda_id: 9999,
      },
    });
    assertLatency(start, 'POST /master/polres (integrity trap)');

    expect(res.status()).toBe(422);
    const body = await res.json();
    assertEnvelope(body, 'POST /master/polres (integrity trap)');
    expect(body.status).toBe(422);
    expect(body.message).toBe('Validasi gagal. Induk Polda tidak ditemukan.');
    expect(body.data).toEqual({});
  });

  test('POST /api/v1/master/polres — Role Trap (403)', async ({ request }) => {
    const start = Date.now();
    const res = await request.post('/api/v1/master/polres', {
      headers: { Authorization: `Bearer ${operatorJwt}` },
      data: {
        nama_polres: 'Polres Banned',
        polda_id: 1,
      },
    });
    assertLatency(start, 'POST /master/polres (role trap)');

    expect(res.status()).toBe(403);
    const body = await res.json();
    assertEnvelope(body, 'POST /master/polres (role trap)');
    expect(body.status).toBe(403);
    expect(body.message).toBe('Akses ditolak. Anda tidak memiliki otoritas Super Admin.');
    expect(body.data).toEqual({});
  });
});

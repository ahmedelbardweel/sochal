<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement('CREATE EXTENSION IF NOT EXISTS pgcrypto');

        DB::statement("DO $$ BEGIN
          CREATE TYPE live_status AS ENUM ('scheduled','live','ending','ended','failed');
        EXCEPTION WHEN duplicate_object THEN NULL; END $$;");

        DB::statement("DO $$ BEGIN
          CREATE TYPE live_visibility AS ENUM ('public','followers','private');
        EXCEPTION WHEN duplicate_object THEN NULL; END $$;");

        DB::statement("DO $$ BEGIN
          CREATE TYPE participant_role AS ENUM ('host','moderator','audience');
        EXCEPTION WHEN duplicate_object THEN NULL; END $$;");

        DB::statement("DO $$ BEGIN
          CREATE TYPE story_type AS ENUM ('image','video','live_replay');
        EXCEPTION WHEN duplicate_object THEN NULL; END $$;");

        DB::statement("CREATE TABLE IF NOT EXISTS live_sessions (
          id                BIGSERIAL PRIMARY KEY,
          host_id           BIGINT NOT NULL,
          title             VARCHAR(120),
          visibility        live_visibility NOT NULL DEFAULT 'public',
          status            live_status NOT NULL DEFAULT 'scheduled',

          room_key          UUID NOT NULL DEFAULT gen_random_uuid(),
          channel_name      VARCHAR(80) NOT NULL,

          started_at        TIMESTAMPTZ,
          ended_at          TIMESTAMPTZ,

          max_viewers       INT,
          is_recording      BOOLEAN NOT NULL DEFAULT FALSE,
          recording_state   VARCHAR(30) NOT NULL DEFAULT 'none',

          created_at        TIMESTAMPTZ NOT NULL DEFAULT NOW(),
          updated_at        TIMESTAMPTZ NOT NULL DEFAULT NOW()
        )");

        DB::statement("CREATE UNIQUE INDEX IF NOT EXISTS uq_live_sessions_room_key ON live_sessions(room_key)");
        DB::statement("CREATE UNIQUE INDEX IF NOT EXISTS uq_live_sessions_channel_name ON live_sessions(channel_name)");
        DB::statement("CREATE INDEX IF NOT EXISTS idx_live_sessions_host_status ON live_sessions(host_id, status)");
        DB::statement("CREATE INDEX IF NOT EXISTS idx_live_sessions_status_started ON live_sessions(status, started_at DESC)");

        DB::statement("CREATE TABLE IF NOT EXISTS live_participants (
          id          BIGSERIAL PRIMARY KEY,
          live_id     BIGINT NOT NULL REFERENCES live_sessions(id) ON DELETE CASCADE,
          user_id     BIGINT NOT NULL,
          role        participant_role NOT NULL DEFAULT 'audience',
          joined_at   TIMESTAMPTZ NOT NULL DEFAULT NOW(),
          left_at     TIMESTAMPTZ,

          device_id   VARCHAR(80),
          ip_hash     VARCHAR(80),

          UNIQUE(live_id, user_id)
        )");

        DB::statement("CREATE INDEX IF NOT EXISTS idx_live_participants_live_left ON live_participants(live_id, left_at)");
        DB::statement("CREATE INDEX IF NOT EXISTS idx_live_participants_user ON live_participants(user_id)");

        DB::statement("CREATE TABLE IF NOT EXISTS live_comments (
          id          BIGSERIAL PRIMARY KEY,
          live_id     BIGINT NOT NULL REFERENCES live_sessions(id) ON DELETE CASCADE,
          user_id     BIGINT NOT NULL,
          message     TEXT NOT NULL,
          created_at  TIMESTAMPTZ NOT NULL DEFAULT NOW(),

          is_deleted  BOOLEAN NOT NULL DEFAULT FALSE,
          deleted_by  BIGINT,
          deleted_at  TIMESTAMPTZ
        )");

        DB::statement("CREATE INDEX IF NOT EXISTS idx_live_comments_live_time
          ON live_comments(live_id, created_at DESC)
          WHERE is_deleted = FALSE");

        DB::statement("CREATE TABLE IF NOT EXISTS live_reaction_buckets (
          id        BIGSERIAL PRIMARY KEY,
          live_id   BIGINT NOT NULL REFERENCES live_sessions(id) ON DELETE CASCADE,
          bucket_ts TIMESTAMPTZ NOT NULL,
          reaction  VARCHAR(20) NOT NULL,
          count     INT NOT NULL DEFAULT 0,
          UNIQUE(live_id, bucket_ts, reaction)
        )");

        DB::statement("CREATE INDEX IF NOT EXISTS idx_live_reaction_buckets_live_ts
          ON live_reaction_buckets(live_id, bucket_ts DESC)");

        DB::statement("CREATE TABLE IF NOT EXISTS live_bans (
          id         BIGSERIAL PRIMARY KEY,
          live_id    BIGINT NOT NULL REFERENCES live_sessions(id) ON DELETE CASCADE,
          user_id    BIGINT NOT NULL,
          banned_by  BIGINT NOT NULL,
          reason     VARCHAR(200),
          created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
          UNIQUE(live_id, user_id)
        )");

        DB::statement("CREATE OR REPLACE FUNCTION set_updated_at()
        RETURNS TRIGGER AS $$
        BEGIN
          NEW.updated_at = NOW();
          RETURN NEW;
        END;
        $$ LANGUAGE plpgsql;");

        DB::statement("DROP TRIGGER IF EXISTS trg_live_sessions_updated ON live_sessions");
        DB::statement("CREATE TRIGGER trg_live_sessions_updated
        BEFORE UPDATE ON live_sessions
        FOR EACH ROW EXECUTE FUNCTION set_updated_at();");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP TABLE IF EXISTS live_bans");
        DB::statement("DROP TABLE IF EXISTS live_reaction_buckets");
        DB::statement("DROP TABLE IF EXISTS live_comments");
        DB::statement("DROP TABLE IF EXISTS live_participants");
        DB::statement("DROP TABLE IF EXISTS live_sessions");
        
        // Caution: Dropping types and extensions might affect other tables if they use them.
        // However, since we are using IF NOT EXISTS and unique names, it should be relatively safe for a prototype.
        // DB::statement("DROP TYPE IF EXISTS live_status");
        // DB::statement("DROP TYPE IF EXISTS live_visibility");
        // DB::statement("DROP TYPE IF EXISTS participant_role");
        // DB::statement("DROP TYPE IF EXISTS story_type");
    }
};

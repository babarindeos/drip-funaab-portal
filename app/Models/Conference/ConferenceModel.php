<?php

namespace App\Models\Conference;

use Illuminate\Database\Eloquent\Model;

/**
 * Base class for every Conference Grant model: they all use the separate
 * conference database instead of the portal's main one.
 *
 * Users are NOT in that database. Relations to User (the applicant, the admin
 * who decided) are loaded with a second query on the main database, so
 * cross-database joins and whereHas() on them are not possible.
 */
abstract class ConferenceModel extends Model
{
    public function getConnectionName()
    {
        return config('conference.connection', 'conference');
    }

    /**
     * Normally Laravel makes a related model that has no connection of its own use
     * the PARENT's connection. For a conference model that is the conference
     * database, which has no users table - so a relation to User would query the
     * wrong database ("Table 'drip_conference.users' doesn't exist").
     *
     * Related models are created plainly instead: conference models already know
     * their own connection (above), and any other model, such as User, keeps the
     * portal's default one.
     */
    protected function newRelatedInstance($class)
    {
        return new $class;
    }
}

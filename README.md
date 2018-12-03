### Setup

 1. Download and extract the WHMCS integration [here](https://install.synergycp.com/bm/integration/whmcs/synergycpreseller.zip).
 2. Copy the entire directory via FTP, SCP, etc. to `/<WHMCS_PATH>/modules/servers/synergycpreseller/`
 3. Go to SynergyCP > Account drop down (top right) > API Keys.
 4. Create an API Key for the Integration, and copy the key.
 5. Go to WHMCS Admin > Setup (Top nav) > Products/Services > Servers
 6. Add New Server
     - Name: SynergyCP
     - Hostname: The hostname of the SynergyCP API - this is usually `api.<SynergyCP URL>`
 7. Scroll down to Server Details
     - Type: Synergy Control Panel - Reseller
     - Username & Password: leave these blank.
     - Access Hash: <API Key from SynergyCP>
 8. Go to Setup > Products/Services > Products/Services.
 9. Create a new Product/Service with type "Reseller Account" and whatever name you want.
 10. Go to Module Settings tab and fill in the following details:
     - Module Name: Synergy Control Panel - Reseller
     - The remaining fields as described on that page.
 11. Go to Custom Fields and create one with the following details:
     - Field Name: SynergyCP Server ID (name must match exactly)
     - Field Type: Text Box
     - Description: The ID of the server form SynergyCP. Found in the manage server page URL.
     - Validation: [0-9]
     - Check Admin Only and Required Field.
 12. Create a test order using the newly created Product/Service and a test client.
 13. Go to SynergyCP, click on a server, and look at the URL. The Server ID is the number that is the last part of the path in the URL (before the question mark).
 14. Copy that Server ID over to the SynergyCP Server ID field of the server in WHMCS.
     - Note that this step will need to be done for every new order to link it to your server in SynergyCP.
 15. Login as the client, view the service, and check that the buttons are working as expected.
     - Bandwidth data can take up to 24 hours to sync. If it has not synced after that point, make sure that the WHMCS cron is setup properly.

<?php
/**
 * Create GitLab Repository
 * @see https://docs.gitlab.com/ce/api/projects.html#create-project
 * 
 * @internal - Since Pantheon does not support custom environment or group 
 * variables, the secrets required for this script's requests would be 
 * exposed if stored in this file. Until ENV vars are supported, or an
 * alternative emerges, the following steps are required to replace this
 * direct script implementation.
 * 
 * 1. A dummy request script has been created to "trigger" the request 
 *    via `zapier/webhook-540021.php`.
 * 2. The request forwards the $_POST and $_SERVER variables to the webhook.
 * 3. An "action" js script (`gitlab/create_gitlab_repo.js`) is run within
 *    the Zapier cloud, completing the intent of this original script since
 *    secrets are defined in a non-vc environment.
 */

require __DIR__ . '/../vendor/autoload.php';

$project_name = $_ENV['PANTHEON_SITE_NAME'];
$namespace_id = 198322; // hardcoded to Barrel's Group
$private_token = 'SECRET///'; // should be protected
$base_url = "https://gitlab.com/api/v4/projects";

$headers = array(
  'Cache-Control' => 'no-cache',
  'PRIVATE-TOKEN' => $private_token
);
$options = array(
  'name' => $project_name,
  'namespace_id' => $namespace_id,
  'visibility' => 'private',
  'issues_enabled' => 'true',
  'merge_requests_enabled' => 'true',
  'jobs_enabled' => 'true',
  'resolve_outdated_diff_discussions' => 'true',
  'printing_merge_request_link_enabled' => 'true',
  'only_allow_merge_if_pipeline_succeeds' => 'true',
  'only_allow_merge_if_all_discussions_are_resolved' => 'true',
  'import_url' => 'https://gitlab.com/barrel/barrel-wordpress.git'
);

try {
  // create new git repo with defaults
  $response = Requests::post($base_url, $headers, $options);
  $data = json_decode( $response->body );

  // setup commit push rules
  if ( $data && isset( $data->id ) ) {
    $options = array(
      'commit_message_regex' => '^[A-Z]',
      'branch_name_regex' => '(master|develop|feature|bugfix|support|hotfix|release)\/?(.*)',
      'file_name_regex' => '(jar|exe|zip|gz|bz2)$',
      'max_file_size' => '2',
    );
    $response = Requests::put("$base_url/{$data->id}/push_rule", $headers, $options);
  }
} catch (Exception $ex) {
  echo $ex;
}

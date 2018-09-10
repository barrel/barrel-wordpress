/**
 * Create GitLab Repository
 * https://docs.gitlab.com/ce/api/projects.html#create-project
 *
 */
const projectName = inputData.name;
const nameSpaceId = inputData.namespace_id;
const privateToken = inputData.private_token;
const baseUrl = inputData.base_url;

output = {
  name: projectName
}

const headers = {
  'Cache-Control': 'no-cache',
  "Content-Type": "application/json; charset=utf-8",
  'PRIVATE-TOKEN': privateToken
}
const options = {
  'name': projectName,
  'namespace_id': nameSpaceId,
  'visibility': 'private',
  'issues_enabled': 'true',
  'merge_requests_enabled': 'true',
  'jobs_enabled': 'true',
  'resolve_outdated_diff_discussions': 'true',
  'printing_merge_request_link_enabled': 'true',
  'only_allow_merge_if_pipeline_succeeds': 'true',
  'only_allow_merge_if_all_discussions_are_resolved': 'true',
  'import_url': 'https://gitlab.com/barrel/barrel-wordpress.git'
}

try {
  // create new git repo with defaults
  const response = await fetch(baseUrl, {
    method: 'POST',
    body: JSON.stringify(options),
    headers: headers
  })
  const data = await response.json()
  const projectId = await data.id

  output.jobs = [
    {
      id: projectId,
      status: typeof( projectId ),
      data: data
    }
  ]

  // setup commit push rules
  if ( data && typeof( projectId ) !== 'undefined' ) {
    let options = {
      'commit_message_regex': '^[A-Z]',
      'branch_name_regex': '(master|develop|feature|bugfix|support|hotfix|release)\/?(.*)',
      'file_name_regex': '(jar|exe|zip|gz|bz2)$',
      'max_file_size': '2',
    }
    let pushRuleUrl = `${baseUrl}/${projectId}/push_rule`
    const response = await fetch(pushRuleUrl, {
      method: 'PUT',
      body: JSON.stringify(options),
      headers: headers
    })
    output.jobs.push({
      pushRule: pushRuleUrl,
      data: await response.json()
    })
  }
} catch (exception) {
  console.log(exception)
}

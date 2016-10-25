<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Challenge Rest API</title>
    <link rel="stylesheet" href="style/index.css" media="screen" charset="utf-8">
  </head>
  <body>
    <h2>User list</h2>
    <table>
      <thead>
        <tr>
          <td>Id</td>
          <td>Name</td>
          <td>Mail</td>
          <td>Loved tracks</td>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($data['users'] as $user) { ?>
          <tr>
            <td><?php echo $user['id']; ?></td>
            <td><?php echo $user['name']; ?></td>
            <td><?php echo $user['mail']; ?></td>
            <td>
              <ul>
                <?php foreach ($user['loved'] as $lovedTrack) { ?>
                  <li><?php echo '[' . $lovedTrack['id'] . '] ' . $lovedTrack['name'] . ' - ' . $lovedTrack['duration'] . 's'; ?></li>
                <?php } ?>
              </ul>
            </td>
          </tr>
        <?php } ?>
      </tbody>
    </table>
    <h2>Track list</h2>
    <table>
      <thead>
        <tr>
          <td>Id</td>
          <td>Name</td>
          <td>Duration</td>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($data['tracks'] as $track) { ?>
          <tr>
            <td><?php echo $track['id']; ?></td>
            <td><?php echo $track['name']; ?></td>
            <td><?php echo $track['duration']; ?></td>
          </tr>
        <?php } ?>
      </tbody>
    </table>
  </body>
</html>

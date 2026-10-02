<?php

$name = $_POST['name'];
$visitor_email = $_POST['email'];
$message = $_POST['message'];

//Validate first
if(empty($name)||empty($visitor_email))
{
		echo '<script type="text/javascript">
	    alert("Nombre y Correo electronico son necesarios.");
		    history.back();
		  </script>';
    return;
}

if(IsInjected($visitor_email))
{
	echo '<script type="text/javascript">
		alert("Formato de correo electronico incorrecto.");
			history.back();
		</script>';
	return;
}

$email_from = "$visitor_email";//<== update the email address
$email_subject = "Mensaje desde efictalia.com.mx";
$email_body = "Nombre: $name\n\n".
"Mensaje: $message\n\n".
$headers = "Email: $email_from \n\n";
$to = "efictalia@efictalia.com.mx";//<== update the email address
//Send the email!
mail($to,$email_subject,$email_body,$headers);
//done. redirect to thank-you page.
header('Location: respuesta.html');


// Function to validate against any email injection attempts
function IsInjected($str)
{
  $injections = array('(\n+)',
              '(\r+)',
              '(\t+)',
              '(%0A+)',
              '(%0D+)',
              '(%08+)',
              '(%09+)'
              );
  $inject = join('|', $injections);
  $inject = "/$inject/i";
  if(preg_match($inject,$str))
    {
    return true;
  }
  else
    {
    return false;
  }
}

?>

<?php header('Content-Type: text/html; charset=utf-8'); 
ini_set('display_errors',1);
ini_set('display_startup_erros',1);
error_reporting(E_ALL);
// Inclui o arquivo class.phpmailer.php localizado na pasta phpmailer
require("class.phpmailer.php");
require("class.smtp.php");

//Defino a Chave do meu site
$secret_key = '6Ld3a2MaAAAAANgTzuoN_TY8mmpS5exonDjiWw9v';

//Pego a validação do Captcha feita pelo usuário
$recaptcha_response = $_POST['g-recaptcha-response'];

// Verifico se foi feita a postagem do Captcha 
if(isset($recaptcha_response)){
        
    // Valido se a ação do usuário foi correta junto ao google
    $answer = 
        json_decode(
            file_get_contents(
                'https://www.google.com/recaptcha/api/siteverify?secret='.$secret_key.
                '&response='.$_POST['g-recaptcha-response']
            )
        );

    // Se a ação do usuário foi correta executo o restante do meu formulário
    if($answer->success) {
        // Inicia a classe PHPMailer
        $mail = new PHPMailer();
        $nome = $_POST["nome"];
        $email = $_POST["email"];
        $celular = $_POST["celular"];
        $mensagem = $_POST["mensagem"];
        if (isset($_FILES['arquivo'])) {
            $arquivo = $_FILES['arquivo'];
        }
        // Define os dados do servidor e tipo de conexão
        // =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
        $mail->IsSMTP(); // Define que a mensagem será SMTP
        $mail->Host = "smtp.umbler.com";
        $mail->SMTPAuth = true; // Usar autenticação SMTP (obrigatório para smtp.seudomínio.com.br)
        $mail->Username = 'site@alberione.com.br'; // Usuário do servidor SMTP (endereço de email)
        $mail->Password = 'Alter@r204'; // Senha do servidor SMTP (senha do email usado)
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );
        // Define o remetente
        // =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
        $mail->From = $email; // Seu e-mail
        $mail->Sender = 'site@alberione.com.br'; // Seu e-mail
        $mail->FromName =  $nome; // Seu nome
        // Define os destinatário(s)
        //$mail->AddAddress($email);
        // $mail->AddAddress('site@novaeraweb.com.br'); // Copia
        $mail->AddAddress('contato@novaeraweb.com.br', 'Site'); // Copia
        $mail->AddBCC('contato@novaeraweb.com.br', 'Site'); // Cópia Oculta
        // Define os dados técnicos da Mensagem
        // =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
        $mail->IsHTML(true); // Define que o e-mail será enviado como HTML
        $mail->CharSet = 'utf-8'; // Charset da mensagem (opcional)
        // Define a mensagem (Texto e Assunto)
        // =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
        $mail->Subject  = "Lead do Site | Alberione Advocacia"; // Assunto da mensagem 
        $mail->Body = '
        Lead captado através do site:<br><br>
        Nome: '.$nome.'<br>
        Celular: '.$celular.'<br>
        Email: '.$email.'<br><br>
        Mensagem:<br>
        '.$mensagem.'

        ';
        $mail->AltBody = '
        Lead captado através do site:<br><br>
        Nome: '.$nome.'<br>
        Celular: '.$celular.'<br>
        Email: '.$email.'<br><br>
        Mensagem:<br>
        '.$mensagem.'
        ';
        if (isset($_FILES['arquivo'])) {
            $mail->AddAttachment($arquivo['tmp_name'], $arquivo['name']);
        }
    // anexar arquivo
    // $mail->AddAttachment($arquivo['tmp_name'], $arquivo['name']);
    //==================================================== 
        if ($enviado = $mail->Send()){ 
                header("Location: ../index-form.php?enviado=true");
            } 
            else
            { 
                echo "</b>Falha no envio do E-Mail!</b>"; 
                echo "<b>Informações do erro:</b> " . $mail->ErrorInfo;
            }
        }
}
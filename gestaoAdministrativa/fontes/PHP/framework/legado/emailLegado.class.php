<?php
/*
    **********************************************************************************
    *                                                                                *
    * @package URBEM CNM - Soluções em Gestão Pública                                *
    * @copyright (c) 2013 Confederação Nacional de Municípos                         *
    * @author Confederação Nacional de Municípios                                    *
    *                                                                                *
    * O URBEM CNM é um software livre; você pode redistribuí-lo e/ou modificá-lo sob *
    * os  termos  da Licença Pública Geral GNU conforme  publicada  pela Fundação do *
    * Software Livre (FSF - Free Software Foundation); na versão 2 da Licença.       *
    *                                                                                *
    * Este  programa  é  distribuído  na  expectativa  de  que  seja  útil,   porém, *
    * SEM NENHUMA GARANTIA; nem mesmo a garantia implícita  de  COMERCIABILIDADE  OU *
    * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral do GNU *
    * para mais detalhes.                                                            *
    *                                                                                *
    * Você deve ter recebido uma cópia da Licença Pública Geral do GNU "LICENCA.txt" *
    * com  este  programa; se não, escreva para  a  Free  Software Foundation  Inc., *
    * no endereço 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.       *
    *                                                                                *
    **********************************************************************************
*/
?>
<?php
/**
*
* Data de Criação: 27/10/2005

* @author Desenvolvedor: Cassiano de Vasconcellos Ferreira
* @author Documentor: Cassiano de Vasconcellos Ferreira

* @package framework
* @subpackage componentes

Casos de uso: uc-01.01.00
*/
?>

<?php
    class emailLegado
    {
        /*** Variáveis ***/
            var $remetente;
            var $destinatario;
            var $assunto;
            var $corpo;
            var $anexo;
            var $erro;

        /*** Método Construtor ***/
            function emailLegado()
            {
                $this->remetente = "";
                $this->destinatario = "";
                $this->assunto = "";
                $this->corpo = "";
                $this->anexo = "";
                $this->erro = "";
            }

        /*** Método que seta variáveis para o email ***/
            function setaVariaveisEmail($rem, $dest,$ass,$corp)
            {
                $this->remetente = $rem;
                $this->destinatario = $dest;
                $this->assunto = $ass;
                $this->corpo = $corp;
            }

        /*** Método que seta variáveis para o email com anexo***/
            function setaVariaveisAnexo($rem,$dest,$ass,$corp,$anex)
            {
                $this->remetente = $rem;
                $this->destinatario = $dest;
                $this->assunto = $ass;
                $this->corpo = $corp;
                $this->anexo = $anex;
            }

        /*** Método que seleciona o Remetente ***/
            function selecionaRemetente()
            {
                $rem = Sessao::read('numCgm');
                $dbConfig = new dataBaseLegado;
                $dbConfig->abreBd();
                $select =   "select e_mail
                                from sw_cgm as c
                                where c.numcgm = '$rem'";
                $dbConfig->abreSelecao($select);
                $rem = $dbConfig->pegaCampo("e_mail");
                $dbConfig->limpaSelecao();

                return $rem;
            }

        /*** Método que envia emails com ou sem anexo ***/
            function enviaEmail()
            {
                if ($this->remetente != "") {
                    if (mail("$this->destinatario","$this->assunto","$this->corpo","From: $this->remetente\n"))
                        return true;
                    else
                        return false;
                } else

                    return false;
            }

        /*** Método que envia arquivo em anexo no email ***/
                function enviaAnexo()
                {
                    if ($this->anexo != "") { // Se existir arquivo anexo para ser enviado ele entra neste if !
                        $mime_list = array("pdf"=>"Aplication/pdf","zip"=>"Aplication/zip"); // Uma lista de tipos de arquivo que podaram ser enviados em anexo !
                        $ABORT = FALSE;
                        $data = "XYZ-" . date(dmyhms) . "-ZYX";
                        // Mensagem do e-mail para o script entender que é um e-mail com anexo !
                        $message = "--$data\n";
                        $message .= "Content-Transfer-Encoding: 8bits\n";
                        $message .= "Content-Type: text/plain; charset=\"UTF-8\"\n\n";
                        $message .= $this->corpo;
                        $message .= "\n";
                        $vet_anexo[1] = $this->anexo;// Pega o caminho completo do nome do arquivo !
                        foreach ($vet_anexo as $chave => $caminho) {//Checa se o arquivo que será anexado existe e este será codificado !
                        if ($caminho !='') {
                            if (file_exists($caminho)) {
                                if ($arq = fopen($caminho,"rb")) {//Tentando abrir o aquivo
                                    $arq_nome = array_pop(explode(chr(92),$caminho));//Pega o nome do aquivo apartir do seu caminho
                                    $conteudo = fread($arq,filesize($caminho));
                                    $codificado = base64_encode($conteudo);//Codifica os dados do Arquivo
                                    $codificado_split = chunk_split($codificado);//*****SPLIT(Separando ou quebrando os dados codificados)*****
                                    fclose($arq);
                                    $message .= "--$data\n";
                                    $message .= "Content-Type: $anexo_type\n";
                                    $message .= "Content-Disposition: attachment; filename=\"$anexo_name\" \n";
                                    $message .= "Content-Transfer-Encoding: base64\n\n";
                                    $message .= "$codificado_split\n";
                                } else {
                                    $this->erro = "Não foi possivel abrir o Arquivo $chave: $arq_nome";
                                    $ABORT = TRUE; // $ABORD = TRUE significa que o script ira parar neste ponto !
                                }
                            } else {
                                $this->erro = "O Arquivo $chave Não Exite: $arq_nome";
                                $ABORT = TRUE;
                            }
                            }
                        }
                        $message .= "--$data--\r\n";
                        $topo = "MIME-Version: 1.0\n";
                        $topo .= "From: <$this->remetente>\r\n";
                        $topo .= "Content-type: multipart/mixed; boundary=\"$data\"\r\n";
                        $mensagem = mail("$this->destinatario", "$this->assunto", "$message", "$topo ");
                        if ($mensagem) {
                            $this->erro = "Mensagem enviada!";
                        } else {
                            $this->erro = "O envio da mensagem falhou!";
                        }
                    } else {
                        $this->erro = "Você deixou um dos campos do formulário vazio!!";
                    }
        }
    }

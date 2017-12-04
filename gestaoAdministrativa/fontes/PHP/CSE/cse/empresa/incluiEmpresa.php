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
* Arquivo de instância para Empresa
* Data de Criação: 27/02/2003

* @author Analista:
* @author Desenvolvedor: Ricardo Lopes de Alencar

* @package URBEM
* @subpackage

$Revision: 19057 $
$Name$
$Author: rodrigo_sr $
$Date: 2007-01-03 08:56:17 -0200 (Qua, 03 Jan 2007) $

* Casos de uso: uc-01.07.89
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
  include_once (CAM_FW_LEGADO."funcoesLegado.lib.php"      );
  include_once '../cse.class.php';
  include_once (CAM_FW_LEGADO."paginacaoLegada.class.php"  );
  include_once (CAM_FW_LEGADO."auditoriaLegada.class.php"  ); //Inclui classe para inserir auditoria

if (!isset($controle)) {
    $controle = 0;
    $sessao->transf = "";
}

switch ($controle) {
//Formulário em HTML para entrada de dados
case 0:
?>
<script type="text/javascript">
    function Valida()
    {
        var mensagem = "";
        var erro = false;
        var campo;
        var campoaux;
        var f = document.frm;

        campo = document.frm.cnpj.value.length;
        if (campo == 0) { // Campo cnpj tem que ter 14 caracteres >
            mensagem += "@Campo CNPJ inválido!()";
            erro = true;
        }

        var myRegExp = new RegExp("[^0-9a-zA-Z]", "ig");
        campoaux = document.frm.cnpj.value.replace(myRegExp,'');
        if (campo==18) {
            if (!VerificaCNPJ(campoaux)) { //> Verifica se o CNPJ é válido
                mensagem += "@Campo CNPJ inválido!("+document.frm.cnpj.value+")";
                erro = true;
            }
        }

        campo = f.nomEmpresa.value.length;
        if (campo==0) {
            mensagem += "@Campo Nome da Empresa inválido!()";
            erro = true;
        }

        if (erro) alertaAviso(mensagem,'form','erro','<?=$sessao->id;?>','');
        return !(erro);
    }// Fim da function Valida

    //A função salvar testa a validação, e se tudo ocorrer certo, envia o form
    function Salvar()
    {
        document.frm.ok.disabled = true;
        if (Valida()) {
            document.frm.submit();
        } else {
            document.frm.ok.disabled = false;
        }
    }
</script>
<form name='frm' method='post' action='<?=$PHP_SELF;?>?<?=$sessao->id;?>' target='oculto'>
<input type='hidden' name='controle' value='1'>
<table width='100%'>
    <tr>
        <td class="alt_dados" colspan="2">
            Empresa
        </td>
    </tr>
    <tr>
        <td class='label' width='20%' title="Nome da empresa">
            *Nome
        </td>
        <td class='field' width='80%'>
            <input type='text' name='nomEmpresa' value='' size='40' maxlength='80' onKeyUp="return autoTab(this, 80, event);" >
        </td>
    </tr>
    <tr>
        <td class="label">
            *CNPJ
        </td>
        <td class="field">
            <input type="text" name="cnpj" maxlength="18" size="20" value="<?=$cnpj;?>"
            onKeyPress="return(isValido(this, event, '0123456789'));"
            onKeyUp = "JavaScript: mascaraCNPJ(this, event);return autoTab(this, 18, event);">
        </td>
    </tr>
    <tr>
        <td colspan='2' class='field'>
            <?php geraBotaoOk(); ?>
        </td>
    </tr>
</table>
</form>
<?php
    break;
    //Inclusão, alteração ou exclusão de dados
    case 1:
        $js = "";
        $ok = true;
        $vet = $_POST;
        $vet[cnpj] = preg_replace( "/[^0-9a-zA-Z]/","", $cnpj);
        //Verifica se já existe o registro a ser incluido
        if (!comparaValor("nom_empresa", urlencode($nomEmpresa), "cse.empresa","",1)) {
            $js .= "mensagem += '@O nome ".urlencode($nomEmpresa)." já existe!'; \n";
            $ok = false;
        }

        //Verifica se já existe o CNPJ a ser incluido
        if (!comparaValor("cnpj", $vet[cnpj], "cse.empresa","",1)) {
            $js .= "mensagem += '@O CNPJ ".$cnpj." já está cadastrado'; \n";
            $ok = false;
        }
    /*** Se não houver restrições faz a inclusão dos dados ***/
        if ($ok) {
            $cse = new cse();

            $objeto = urlencode($nomEmpresa);

            if ($cse->incluirEmpresa($vet) ) {
                //Insere auditoria
                $audicao = new auditoriaLegada;
                $audicao->setaAuditoria($sessao->numCgm, $sessao->acao, $objeto);
                $audicao->insereAuditoria();
                //Exibe mensagem e retorna para a página padrão
                alertaAviso($PHP_SELF,htmlentities($objeto),"incluir","aviso","");
            } else {
                exibeAviso($objeto,"n_incluir","erro");
                $js .= "f.ok.disabled = false; \n";
            }
        } else {
            $js .= "f.ok.disabled = false; \n";
            $js .= "erro = true; \n";
        }
    break;
}//Fim switch

?>
<html>
<head>
<script type="text/javascript">
function executa()
{
    var mensagem = "";
    var erro = false;
    var f = window.parent.frames["telaPrincipal"].document.frm;
    var d = window.parent.frames["telaPrincipal"].document;
    var aux;
    <?php echo $js; ?>

    if (erro) alertaAviso(mensagem,'form','erro','<?=$sessao->id;?>','');
}
</script>
</head>

<body onLoad="javascript:executa();">

</body>
</html>
<?php
include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php'; //Insere o fim da página html
?>

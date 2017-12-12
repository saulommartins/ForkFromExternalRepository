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
    * Arquivo que faz a alteração das configurações básicas do módulo Patrimônio
    * Data de Criação   : 24/03/2003

    * @author Desenvolvedor Leonardo Tremper

    * @ignore

    $Revision: 25028 $
    $Name$
    $Autor: $
    $Date: 2007-08-23 10:54:58 -0300 (Qui, 23 Ago 2007) $

    * Casos de uso: uc-03.01.01
*/

/*
$Log$
Revision 1.23  2007/08/23 13:54:58  bruce
Corrigi um rótulo

Revision 1.22  2007/06/04 13:26:46  rodrigo
Bug #8987#

Revision 1.21  2006/11/14 10:14:32  larocca
Bug #6902#

Revision 1.20  2006/08/14 12:04:56  fernando
Alterações para funcionar o ajuda.

Revision 1.19  2006/08/07 19:02:21  fernando
Alterações para funcionar o ajuda.

Revision 1.18  2006/07/18 14:01:28  fernando
retirado  campo caminho do xml

Revision 1.17  2006/07/18 13:08:39  fernando
alteração de hint

Revision 1.16  2006/07/06 14:06:58  diego
Retirada tag de log com erro.

Revision 1.15  2006/07/06 12:11:28  diego

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/funcoesLegado.lib.php';
$exercicio = Sessao::getExercicio();
if (!(isset($ctrl)))
$ctrl = 0;

switch ($ctrl) {
case 0:

    $dataAtualizacao = pegaConfiguracao("atualizacao_valor","6");
    $textoFicha      = pegaConfiguracao("texto_ficha_transferencia","6");
    $xmlCaminho      = pegaConfiguracao("xml_rodape_patrimonial","6");

    $sSQL =" SELECT valor from administracao.configuracao where exercicio = ".$exercicio." and cod_modulo = 6 and parametro = 'grupo_contas_permanente';";
    $conn = new dataBaseLegado;
    $conn->abreBD();
    $conn->abreSelecao($sSQL);
    $conn->vaiPrimeiro();
    $codEstrutural = trim($conn->pegaCampo("valor"));

    $conn->limpaSelecao();
    $conn->fechaBD();

    $sSQL="SELECT nom_conta from contabilidade.plano_conta where cod_estrutural = '".$codEstrutural."' and exercicio = ".$exercicio.";";
    $conn = new dataBaseLegado;
    $conn->abreBD();
    $conn->abreSelecao($sSQL);
    $conn->vaiPrimeiro();
    $nomConta = trim($conn->pegaCampo("nom_conta"));
    $conn->limpaSelecao();
    $conn->fechaBD();

      if (strlen($nomConta) > 0) {
                    $js .= "document.getElementById('nomConta').innerHTML = '".$nomConta."';\n";
//                    $js .= "document.frm.depreciacao.focus();";
//                    $js .= "alert('Rodrigo lango lango');";

                }
                $jsOnLoad = $js;
//                  executaFrameOculto($js);

?>
    <script type="text/javascript">

        function Valida()
        {
            var mensagem = "";
            var erro = false;
            var campo;
            var campoaux;

            campo = document.frm.xmlCaminho.value.length;
                if (campo == 0) {
                mensagem += "@O campo Caminho do XML é obrigatório.";
                erro = true;
            }

            campo = document.frm.codEstrutural.value.length;
                if (campo == 0) {
                mensagem += "@O campo Conta do Ativo Permanente é obrigatório.";
                erro = true;
            }

            campo = document.frm.textoFicha.value.length;
                if (campo == 0) {
                mensagem += "@O campo Texto da Transferência é obrigatório.";
                erro = true;
            }
            if (erro) alertaAviso(mensagem,'form','erro','<?=Sessao::getId()?>');
                return !(erro);
        }

        function Cancela()
        {
         document.frm.reset();
         validacao(document.frm.codEstrutural.value);
        }

        function Salvar()
        {
                if (Valida()) {
        document.frm.target = "oculto";
                        document.frm.submit();
                }
        }

        function validacao(cod)
        {
            if (cod) {
                document.frm.target         = "oculto";
                document.frm.ctrl.value     = 3;
                document.frm.controle.value = 1;
                document.frm.submit();
                document.frm.target         = "principal";
                document.frm.ctrl.value     = 1;
                document.frm.controle.value = 0;
            } else {
                document.getElementById('nomConta').innerHTML = '&nbsp;';
            }
        }
    </script>

    <form name="frm" action="configuracaoBasica.php?<?=Sessao::getId()?>" method="POST">
        <table width="100%">

            <tr>
                <td class="alt_dados" colspan=2>
                    Parâmetros do Módulo Patrimônio
                </td>
            </tr>
<!--			<tr>
                <td class="label" width="20%" title="Informe o caminho do arquivo XML do rodapé do relatório patrimonial.">
                    *Caminho do Rodapé
                </td>
                <td class="field" width="80%">
                    <input type="text" name="xmlCaminho" value="<?=$xmlCaminho;?>" size="80" maxlenght="80"> -->
                    <input type="hidden" name="xmlCaminho" value="<?=$xmlCaminho;?>" size="80" maxlenght="80">
                    <input type="hidden" name="ctrl" value=1>
                    <input type="hidden" name="controle" value=0>
<!--				</td>
            </tr>    -->
            <tr>
                <td class="label" title="Informe o texto da ficha de transferência.">
                    *Texto da Transferência
                </td>
                <td class="field">
                    <textarea name="textoFicha" rows="5" cols="50"><?=$textoFicha;?></textarea>
                </td>
            </tr>

    <tr>
        <td class='label'title="Selecione a conta do ativo permanente.">*Conta do Ativo Permanente</td>
        <td class='field' valign="top">
            <table width="100%" cellpadding="0" cellspacing="0" border="0">

            <tr>
            <td align="left" width="11%" valign="top">
                <input type='text' id='codEstrutural' name='codEstrutural'
                value='<?=$codEstrutural;?>' size='25' maxlength='25'
                onchange="JavaScript:preencheComZeros('9.9.9.9.9.99.99.99.99.99', this, 'D');"
                onkeypress="JavaScript:return validaExpressao(this,event,'[0-9.]');"
                onkeyup="JavaScript:mascaraDinamico('9.9.9.9.9.99.99.99.99.99', this, event);"
                onBlur="validacao(this.value);"
                onKeyPress="return(isValido(this, event, '0123456789-'))">
                <input type="hidden" name="nomConta" value="<?=$nomConta;?>">
            </td>
            <td width="1">&nbsp;</td>
            <td align="left" width="55%" id="nomConta" class="fakefield"  valign="middle">&nbsp;</td>

            <td align="left" valign="top">
                &nbsp;
                <a href="javascript:abrePopUp('../../../../../../gestaoFinanceira/fontes/PHP/contabilidade/popups/planoConta/FLPlanoConta.php','frm','codEstrutural','nomConta','estrutural','<?php echo Sessao::getId()?>','800','550');">
                <img  title="Buscar contas"  src="../../../../../../gestaoAdministrativa/fontes/PHP/framework/temas/padrao/imagens/procuracgm.gif" border="0" align="absmiddle">
                </a>
            </td>
            </tr>

            </table>
        </td>
    </tr>
<?php
            $sSQL =" SELECT configuracao.valor                                            \n";
            $sSQL.="   FROM administracao.configuracao                                    \n";
            $sSQL.="  WHERE configuracao.exercicio  = '".$exercicio."'                    \n";
            $sSQL.="    AND configuracao.cod_modulo = 6                                   \n";
            $sSQL.="    AND configuracao.parametro  = 'alterar_bens_exercicio_anterior';  \n";

            $conn = new dataBaseLegado;
            $conn->abreBD();
            $conn->abreSelecao($sSQL);
            $conn->vaiPrimeiro();
            (trim($conn->pegaCampo("valor")=="true"))? $checadoS="checked" : $checadoN="checked";
            $conn->limpaSelecao();
            $conn->fechaBD();

?>
            <tr>
                <td class="label" title="Informe o texto da ficha de transferência.">Alterar Bens de Exercício Anterior</td>
                <td width='65%' class='field'>
                   <table border='0' cellpadding=0 cellspacing=0>
                       <tr>
                           <td valign='center'><input type='radio' value="true"  name="alterar_bens_anterior" <?=$checadoS?></td>
                           <td valign='center' style='font-size:12px;'>Sim</td>
                           <td valign='center'><input type='radio' value="false" name="alterar_bens_anterior" <?=$checadoN?>></td>
                           <td valign='center' style='font-size:12px;'>Não</td>
                       </tr>
                   </table>
                </td>
            </tr>
            <tr>
                <td colspan='2' class='field'>
                    <?=geraBotaoAltera();?>
                </td>
            </tr>

        </table>
    </form>

<?php
    break;

case 1:
    include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/configuracaoLegado.class.php';
    $erros = 0;
    $configuracaoLegado = new configuracaoLegado;
    $dbConfig           = new dataBaseLegado;

    $sql = "SELECT *                                       \n";
    $sql.= "  FROM contabilidade.plano_conta               \n";
    $sql.= " WHERE cod_estrutural = '".$codEstrutural."'   \n";
    $sql.= "   AND exercicio      = ".$exercicio.";        \n";

    $ver = true;
    $dbConfig->abreBd();
    $dbConfig->abreSelecao($sql);

    if (!($dbConfig->eof())) {
     $configuracaoLegado->setaChaveValor(texto_ficha_transferencia,$textoFicha,"6");
     $configuracaoLegado->AnoexercicioOrgao = Sessao::getExercicio();
     if ($configuracaoLegado->updateConfiguracao()) {
        $erros = $erros;
     } else {
        $erros++;
     }
     $configuracaoLegado->setaChaveValor(xml_rodape_patrimonial,$xmlCaminho,"6");
     if ($configuracaoLegado->updateConfiguracao()) {
        $erros = $erros;
     } else {
        $erros++;
     }
     if ($configuracaoLegado->updateContaPermanente($codEstrutural,$exercicio)) {
        $erros = $erros;
     } else {
        $erros++;
     }
    } else {
     $ver = false;
     echo '<script>alertaAviso("Conta inexistente('.$codEstrutural.')","n_alterar","erro","'.Sessao::getId().'");</script>';
     //die;
    }
     if ($erros == 0) {
        include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/auditoriaLegada.class.php';
        $audicao = new auditoriaLegada;
        $audicao->setaAuditoria(Sessao::read('numCgm'), $sessao->acao, "Configurações Patrimônio");
        //Registra os passos na auditoria
        //$audicao->insereAuditoria();

        $sql = "UPDATE administracao.configuracao                             \n";
        $sql.= "   SET valor      = '".$_REQUEST['alterar_bens_anterior']."'  \n";
        $sql.= " WHERE cod_modulo = 6                                         \n";
        $sql.= "   AND parametro  = 'alterar_bens_exercicio_anterior'         \n";
        $sql.= "   AND exercicio  = '".$exercicio."';                         \n";

        $dbConfiguracao = new dataBaseLegado;
        $dbConfiguracao->abreBd();
        $dbConfiguracao->abreSelecao($sql);

       if ($ver) {
        echo '<script>parent.window.frames["telaPrincipal"].alertaAviso("Parâmetros","alterar","aviso","'.Sessao::getId().'");</script>';
       }
     } else {
       if ($ver) {
        echo '<script>parent.window.frames["telaPrincipal"].alertaAviso("Configurações","n_alterar","aviso","'.Sessao::getId().'");</script>';
       }
     }
    echo '<script>parent.window.frames["telaPrincipal"].location.href = "configuracaoBasica.php?'.Sessao::getId().'";</script>';
break;

case 3:
    switch ($controle) {
        case 1:
            $sql = "SELECT *                                       \n";
            $sql.= "  FROM contabilidade.plano_conta               \n";
            $sql.= " WHERE cod_estrutural = '".$codEstrutural."'   \n";
            $sql.= "   AND exercicio      = ".$exercicio.";        \n";

            $ver = true;
            $dbConfig = new dataBaseLegado;
            $dbConfig->abreBd();
            $dbConfig->abreSelecao($sql);

            if (!($dbConfig->eof())) {
                echo "<script>parent.window.frames['telaPrincipal'].document.getElementById('nomConta').innerHTML = '".$dbConfig->pegaCampo("nom_conta")."';</script>";
            } else {
                sistemaLegado::exibeAviso("Campo Conta do Ativo Permanente inválido(Código: ".$codEstrutural.")"," "," ");
                echo "<script>parent.window.frames['telaPrincipal'].document.frm.codEstrutural.value = ''; parent.window.frames['telaPrincipal'].document.getElementById('nomConta').innerHTML = '&nbsp;'; parent.window.frames['telaPrincipal'].document.frm.codEstrutural.focus();</script>";
            }
        break;
    }
break;

}
?>

<?php
setAjuda("UC-03.01.01");
//include_once "../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/rodapeLegado.php"
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>

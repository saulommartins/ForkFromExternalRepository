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
    * Insere novos Atributos no sistema de PATRIMÔNIO
    * Data de Criação   : 24/03/2003

    * @author Desenvolvedor Leonardo Tremper

    * @ignore

    $Revision: 28299 $
    $Name$
    $Autor: $
    $Date: 2008-02-29 14:31:59 -0300 (Sex, 29 Fev 2008) $

    * Casos de uso: uc-03.01.02
*/

/*
$Log$
Revision 1.20  2007/04/26 13:46:37  rodrigo_sr
Bug #8331#

Revision 1.19  2006/10/11 15:51:31  larocca
Bug #6903#

Revision 1.18  2006/08/14 17:32:36  fernando
Alterações para funcionar o ajuda.

Revision 1.17  2006/07/13 13:47:50  fernando
Alteração de hints

Revision 1.16  2006/07/06 14:06:58  diego
Retirada tag de log com erro.

Revision 1.15  2006/07/06 12:11:28  diego

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/funcoesLegado.lib.php';
setAjuda("UC-03.01.02");
if (!(isset($ctrl)))
$ctrl = 0;
switch ($ctrl) {
case 0:
?>
   <script type="text/javascript">

      function Valida()
      {
        var mensagem = "";
        var erro = false;
        var campo;
        var campoaux;

        campo = trim(document.frm.nomAtributo.value).length;
            if (campo == 0) {
            mensagem += "@O campo Descrição do Atributo é obrigatório.";
            erro = true;
         }

        campo = document.frm.valorPadrao.value;
            if ((campo == '') && (document.frm.tipo.value=='l')) {
            mensagem += "@O campo Valor Padrão é obrigatório para o tipo Lista.";
            erro = true;
         }

         campo = document.frm.tipo.value;
            if (campo == 'xxx') {
            mensagem += "@O campo Tipo é obrigatório.";
            erro = true;
         }

            if (erro) alertaAviso(mensagem,'form','erro','<?=Sessao::getId()?>');
            return !(erro);
      }

      function Salvar()
      {
        var f = document.frm;
        f.ok.disabled = true;
         if (Valida()) {
            f.submit();
         } else {
            f.ok.disabled = false;
         }
      }

      function frmReset()
      {
            document.frm.reset();
            document.frm.nomAtributo.focus();

            return(true);
        }

   </script>

    <form name="frm" action="incluiAtributo.php?<?=Sessao::getId()?>" method="POST" target='oculto' onreset="return frmReset();">

    <table width='100%'>

        <tr>
            <td colspan="2" class="alt_dados">
                Dados para o Atributo
            </td>
        </tr>

        <tr>
            <td class="label" title="Informe a descrição do atributo." width="20%">
                *Descrição do Atributo
            </td>
            <td class="field" width="80%">
                <input type="text" name="nomAtributo" size="40" maxlength="40" value="<?=$nomAtributo;?>">
                <input type="hidden" name="ctrl" value="1">
            </td>
        </tr>

        <tr>
            <td class="label" title="Selecione o tipo de registro.">
                *Tipo
            </td>
            <td class="field">
                <select name="tipo">
                    <option value="xxx">Selecione</option>
                    <option value="t">Texto</option>
                    <option value="n">Número</option>
                    <option value="l">Lista</option>
                </select>
            </td>
        </tr>

        <tr>
            <td class="label" title="Informe o valor pré-definido para o atributo.">
                Valor Padrão
            </td>
            <td class="field">
                <textarea name="valorPadrao" cols="30" rows="6"><?=$valorPadrao;?></textarea>
            </td>
        </tr>

        <tr>
            <td colspan='2' class='field'>
                <?=geraBotaoOk();?>
            </td>
        </tr>

    </table>

</form>

<?php
break;
case 1:

    //*******************************************************************
    if (comparaValor("nom_atributo", $nomAtributo,"administracao.atributo_dinamico", "",1)) {
    //******************************************************************
    //Se não existir nenhum igual
        include_once '../configPatrimonio.class.php';
        $objeto = "Atributo: ".$nomAtributo;
        $codAtributoId = pegaID("cod_atributo", 'administracao.atributo_dinamico');
        $patrimonio = new configPatrimonio;
        $patrimonio->setaVariaveisAtributos($codAtributoId, $nomAtributo, $tipo, $valorPadrao);
        if ($patrimonio->insereAtributos()) {
            include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/auditoriaLegada.class.php';
            $audicao = new auditoriaLegada;
            $audicao->setaAuditoria(Sessao::read('numCgm'), $sessao->acao, $objeto);
            $audicao->insereAuditoria();
            echo '<script type="text/javascript">
            alertaAviso("'.$objeto.'","incluir","aviso","'.Sessao::getId().'");
            mudaTelaPrincipal("incluiAtributo.php?'.Sessao::getId().'");
            </script>';
        } else {
            echo '<script type="text/javascript">
            alertaAviso("'.$objeto.'","n_incluir","erro","'.Sessao::getId().'");
            mudaTelaPrincipal("incluiAtributo.php?'.Sessao::getId().'");
            </script>';
        }

    //******************************************************************
    } else {
    //******************************************************************
    //Se já existir algum registro com esse nome
    $js = "f.ok.disabled = false;";
    echo '<script type="text/javascript">
    alertaAviso("Já existe um atributo com esse nome","unica","erro","'.Sessao::getId().'");
    </script>';	}//****************************************************************	break;
}

//executaFrameOculto($js);
$JSOnLoad = $js;

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/rodapeLegado.php';
?>

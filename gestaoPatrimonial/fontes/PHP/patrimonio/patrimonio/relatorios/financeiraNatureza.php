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
    * Relatório de posição financeira por Natureza
    * Data de Criação   : 08/04/2003

    * @author Desenvolvedor  Ricardo Lopes de Alencar

    * @ignore

    $Revision: 22607 $
    $Name$
    $Autor: $
    $Date: 2007-05-15 18:56:37 -0300 (Ter, 15 Mai 2007) $

    * Casos de uso: uc-03.01.12
*/

/*
$Log$
Revision 1.21  2007/05/15 21:56:37  leandro.zis
Bug #8347#

Revision 1.20  2006/10/31 13:44:39  larocca
Bug #6775#

Revision 1.19  2006/07/21 11:36:18  fernando
Inclusão do  Ajuda.

Revision 1.18  2006/07/13 20:46:24  fernando
Alteração de hints

Revision 1.17  2006/07/06 14:06:58  diego
Retirada tag de log com erro.

Revision 1.16  2006/07/06 12:11:28  diego

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php'; //Insere o início da página html
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/funcoesLegado.lib.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/dataBaseLegado.class.php';
include_once (CAM_GP."javaScript/ifuncoesJsGP.js");
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/filtrosNGE.inc.php';
setAjuda("UC-03.01.12");

Sessao::remove('filtro');

$anoExercicio = pegaConfiguracao("ano_exercicio");
$xsql = "Select min(cod_bem) as minBem, max(cod_bem) as maxBem From patrimonio.bem";
$codMinimo = pegaValor($xsql,"minBem");
$codMaximo = pegaValor($xsql,"maxBem");
?>
   <script type="text/javascript">

      function Valida()
      {
        var mensagem = "";
        var erro = false;
        var campo;
        var campo2;
        var campoaux;
        var valorMinimo;
        var valorMaximo;

        campo1 = parseInt(document.frm.codInicial.value, 10);
        campo2 = parseInt(document.frm.codFinal.value, 10);

        if (campo1 > campo2) {
            mensagem += "@O campo código inicial não pode ser maior que o campo código final!";
            erro = true;
        }

<?php
        if ($codMinimo>0) {
?>
            valorMinimo = <?=$codMinimo;?>;
            if (campo1 < valorMinimo) { //>
                mensagem += "@O campo código inicial não pode ser menor que "+valorMinimo+".";
                erro = true;
            }

<?php
        }

        if ($codMaximo>0) {
?>
            valorMaximo = <?=$codMaximo;?>;
            if (campo2 > valorMaximo) {
                mensagem += "@O campo código final não pode ser maior que "+valorMaximo+"!";
                erro = true;
            }
<?php
        }
?>
        campo = document.frm.codInicial.value.length;
        if (campo==0) {
            mensagem += "@O campo código inicial é obrigatório.";
            erro = true;
        }

        campo = document.frm.codFinal.value.length;
        if (campo==0) {
            mensagem += "@O campo código final é obrigatório.";
            erro = true;
        }

       if (erro) alertaAviso(mensagem,'form','erro','<?=Sessao::getId()?>');
            return !(erro);
      }

      function Salvar()
      {
         if (Valida()) {
            document.frm.submit();
         }
      }
</script>

<form name="frm" action="financeiraNaturezaMostra.php?<?=Sessao::getId()?>" method="POST">

<table width="100%">
<tr>
    <td class="alt_dados" colspan="2">Selecione o Intervalo</td>
</tr>

<tr>
    <td class="label" width="20%" title="Informe o exercício.">Exercício</td>
    <td class="field">
        <input type="text" name="exercicio" id="idExercicio" value="<?=Sessao::getExercicio()?>" size="10" onKeyPress= "return(isValido(this, event, '0123456789'));"         >
    </td>
</tr>
<tr>
    <td class="label" width="20%" title="Informe o código inicial do bem.">*Código Inicial</td>
    <td class="field">
        <input type="text" name="codInicial" id="idCodInicial" value="<?=$codMinimo;?>" size="10" onKeyPress= "return(isValido(this, event, '0123456789'));"         >
        &nbsp;<a href="javascript:procuraBemGP('frm','codInicial','<?=Sessao::getId()?>');"><img src="../../../../../../gestaoAdministrativa/fontes/PHP/framework/temas/padrao/imagens/procuracgm.gif" title="Buscar bem" align="absmiddle" width="20" height="20" border=0></a>
    </td>
</tr>
<tr>
    <td class="label" title="Informe o código final do bem.">*Código Final</td>
    <td class="field">
        <input type="text" name="codFinal" value="<?=$codMaximo;?>" size="10"  onKeyPress= "return(isValido(this, event, '0123456789'));">
        &nbsp;<a href="javascript:procuraBemGP('frm','codFinal','<?=Sessao::getId()?>');"><img
        src="../../../../../../gestaoAdministrativa/fontes/PHP/framework/temas/padrao/imagens/procuracgm.gif" title="Buscar bem"  align="absmiddle" width="20" height="20" border=0></a>
    </td>
</tr>
<tr>
     <td class="label" width="20%" title="Selecione a entidade para o filtro.">Entidade</td>
     <td class='field' width="80%">
         <select name='codEntidade' style="width:320px">
         <option value='xxx' SELECTED>Selecione</option>
<?php
                    // busca Naturezas cadastradas

            $sSQL = "SELECT
                        OE.cod_entidade
                        , C.numcgm
                        , C.nom_cgm
                    FROM
                        sw_cgm as C,
                        orcamento.entidade AS OE
                    WHERE
                        C.numcgm = OE.numcgm
                    GROUP BY
                          OE.cod_entidade
                        , C.numcgm
                        , C.nom_cgm
                    ORDER BY
                        C.nom_cgm ";
                    $dbEmp = new dataBaseLegado;
                    $dbEmp->abreBD();
                    $dbEmp->abreSelecao($sSQL);
                    $dbEmp->vaiPrimeiro();
                            // monta combo com Entidade
                    $comboCodNatureza = "";
                    while (!$dbEmp->eof()) {
                            $codEntidadef  = trim($dbEmp->pegaCampo("cod_entidade"));
                            $CGM           = trim($dbEmp->pegaCampo("numcgm"));
                            $nomEntidadef  = trim($dbEmp->pegaCampo("nom_cgm"));
                            $chave = $codEntidadef;
                            $dbEmp->vaiProximo();
                            $comboCodEntidade .= "<option value='".$CGM."'";
                            if (isset($codEntidade)) {
                                if ($chave == $codEntidade) {
                                        $comboCodEntidade .= " SELECTED";
                                        $nomEntidade = $nomEntidadef;
                                        }
                                }
                            $comboCodEntidade .= ">".$chave." - ".$nomEntidadef."</option>\n";
                            }
                    $dbEmp->limpaSelecao();
                    $dbEmp->fechaBD();
                    echo $comboCodEntidade;
?>
                </select>
                <input type="hidden" name="nomEntidade" value="">
            </td>
        </tr>
        <tr>
            <td class="label" width="20%" title="Selecione a natureza do bem.">Natureza</td>
            <td class='field' width="80%">
                <select name='codNatureza' onChange="javascript: preencheNGE('codNatureza', this.value);" style="width:320px">
                    <option value="xxx" SELECTED>Selecione</option>
<?php
                    // busca Naturezas cadastradas
                    $sSQL = "SELECT
                                cod_natureza, nom_natureza
                                FROM
                                patrimonio.natureza
                                ORDER
                                by nom_natureza";
                    $dbEmp = new dataBaseLegado;
                    $dbEmp->abreBD();
                    $dbEmp->abreSelecao($sSQL);
                    $dbEmp->vaiPrimeiro();
                            // monta combo com Naturezas
                    $comboCodNatureza = "";
                    while (!$dbEmp->eof()) {
                            $codNaturezaf  = trim($dbEmp->pegaCampo("cod_natureza"));
                            $nomNaturezaf  = trim($dbEmp->pegaCampo("nom_natureza"));
                            $chave = $codNaturezaf;
                            $dbEmp->vaiProximo();
                            $comboCodNatureza .= "<option value='".$chave."'";
                            if (isset($codNatureza)) {
                                if ($chave == $codNatureza) {
                                        $comboCodNatureza .= " SELECTED";
                                        $nomNatureza = $nomNaturezaf;
                                        }
                                }
                            $comboCodNatureza .= ">".$nomNaturezaf."</option>\n";
                            }
                            $dbEmp->limpaSelecao();
                    $dbEmp->fechaBD();
                    echo $comboCodNatureza;
?>
                </select>
                <input type="hidden" name="nomNatureza" value="">
            </td>
        </tr>

<tr>
   <td class="field" colspan="2"><input type="button" value="OK" style="width: 60px;" onClick="Salvar();">
   <input type="reset" value="Limpar" style="width: 60px;" tabindex="1" name="limpar"/></td>
</tr>
</table>

</form>

<?php
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/rodapeLegado.php'; //Insere o fim da página html

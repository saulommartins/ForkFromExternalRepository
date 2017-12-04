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
    * Aqruivo que faz a exclusão dos grupos
    * Data de Criação   : 24/03/2003

    * @author Desenvolvedor Leonardo Tremper
    * @author Desenvolvedor Marcelo B. Paulino

    * @ignore

    $Revision: 20521 $
    $Name$
    $Autor: $
    $Date: 2007-02-28 15:47:47 -0300 (Qua, 28 Fev 2007) $

    * Casos de uso: uc-03.01.04
*/

/*
$Log$
Revision 1.22  2007/02/28 18:47:47  bruce
Bug #8327#

Revision 1.21  2006/10/11 16:03:56  larocca
Bug #6907#

Revision 1.20  2006/07/21 11:35:56  fernando
Inclusão do  Ajuda.

Revision 1.19  2006/07/13 20:23:19  fernando
Alteração de hints

Revision 1.18  2006/07/13 14:20:17  fernando
Alteração de hints

Revision 1.17  2006/07/06 14:06:58  diego
Retirada tag de log com erro.

Revision 1.16  2006/07/06 12:11:28  diego

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/dataBaseLegado.class.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/funcoesLegado.lib.php';
setAjuda("UC-03.01.04");
$anoE = Sessao::getExercicio();

if (!(isset($ctrl)))
    $ctrl = 0;

if (isset($chave)) {
    $ctrl = 2;
    $pagina = $sessao->transf2;
}
?>
<script src="../../../../../../gestaoAdministrativa/fontes/javaScript/ifuncoesJs.js" type="text/javascript"></script>
<script src="../../../../../../gestaoAdministrativa/fontes/javaScript/funcoesJs.js" type="text/javascript"></script>

<?php

switch ($ctrl) {

    // filtra natureza dos grupos a serem filtrados
    case 0:

?>
        <script type="text/javascript">

            // submete formulario
            function Salvar()
            {
                document.frm2.submit();
            }

            // desabilita botao 'OK' se o codNatureza informado nao existir e vice-versa
            function verificaNatureza(campo_a, campo_b)
            {
                var aux;

                aux = preencheCampo(campo_a, campo_b);

                if (aux == false) {
                    document.frm2.ok.disabled = true;

                } else {
                    document.frm2.ok.disabled = false;
                }
            }

        </script>

        <form name="frm2" action="excluiGrupo.php?<?=Sessao::getId()?>" method="POST">

            <input type="hidden" name="ctrl" value="1">

        <table width="100%">

        <tr>
            <td colspan="2" class="alt_dados">Filtrar Grupo</td>
        </tr>

        <tr>
            <td class="label" title="Selecione a natureza do bem." width="20%">*Natureza</td>
            <td class="field" width="80%">

                <input type="text" name="codTxtNatureza" value="<?=$codNatureza != "xxx" ? $codNatureza : "";?>"
                    size="10" maxlength="10" onChange="javascript: verificaNatureza(this, document.frm2.codNatureza);"
                    onKeyPress="return(isValido(this, event, '0123456789'));">

                <select name="codNatureza" onchange="verificaNatureza(this, document.frm2.codTxtNatureza);">
                    <option value="xxx" SELECTED>Selecione</option>
<?php
                    $sSQL = "SELECT * FROM patrimonio.natureza ORDER by nom_natureza";
                    $conn = new dataBaseLegado;
                    $conn->abreBD();
                    $conn->abreSelecao($sSQL);
                    $conn->vaiPrimeiro();
                    $comboNatureza = "";
                    while (!$conn->eof()) {
                        $codNaturezaf  = trim($conn->pegaCampo("cod_natureza"));
                        $nomNatureza  = trim($conn->pegaCampo("nom_natureza"));
                        $conn->vaiProximo();
                        $comboNatureza .= "<option value=".$codNaturezaf;
                            if (isset($codNatureza)) {
                                if ($codNaturezaf == $codNatureza)
                                    $comboNatureza .= " SELECTED";
                            }
                        $comboNatureza .= ">".$nomNatureza."</option>\n";
                    }
                    $conn->limpaSelecao();
                    $conn->fechaBD();
                    echo $comboNatureza;

?>
                </select>
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

        // se a variavel codNatureza estiver setada monta filtro para a consulta
        if ($codNatureza != 'xxx') {
            $filtrarNatureza = "and g.cod_natureza = ".$codNatureza;
        } else {
            $filtrarNatureza = "";
        }

        $sSQLs = "
                SELECT
                    g.cod_grupo, g.cod_natureza, g.nom_grupo
                    , n.nom_natureza
                    ,ga.exercicio, ga.cod_plano
                FROM
                    patrimonio.grupo as g
                    ,patrimonio.natureza as n
                    , patrimonio.grupo_plano_analitica as ga
                WHERE g.cod_natureza = n.cod_natureza AND
                    g.cod_natureza = ga.cod_natureza AND
                    g.cod_grupo = ga.cod_grupo
                    $filtrarNatureza
                ";

        if (!isset($pagina)) {
            $sessao->transf = $sSQLs;
        }

        include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/paginacaoLegada.class.php';
        $paginacao = new paginacaoLegada;
        $paginacao->pegaDados($sessao->transf,"10");
        $paginacao->pegaPagina($pagina);
        $paginacao->complemento="&ctrl=1";
        $paginacao->geraLinks();
        $paginacao->pegaOrder("nom_grupo","ASC");
        $sSQL = $paginacao->geraSQL();

//        print $sSQL;
        $conn = new dataBaseLegado;
        $conn->abreBD();
        $conn->abreSelecao($sSQL);

        if ( $pagina > 0 and $conn->eof() ) {
            $pagina--;
            $paginacao->pegaPagina($pagina);
            $paginacao->complemento="&ctrl=1";
            $paginacao->geraLinks();
            $paginacao->pegaOrder("nom_grupo","ASC");
            $sSQL = $paginacao->geraSQL();
            $conn->abreSelecao($sSQL);
        }

        $conn->vaiPrimeiro();

        $sessao->transf2 = $pagina;

?>
        <table width="100%">
        <tr>
            <td class="alt_dados" colspan="6">Registros do Grupo</td>
        </tr>
        <tr>
            <td class="labelcenter" width="5%" rowspan="2">&nbsp;</td>
            <td class="labelcenter" width="45%" colspan="2">Natureza</td>
            <td class="labelcenter" width="45%" colspan="2">Grupo</td>
            <td class="labelcenter" width="1%" rowspan="2">&nbsp;</td>
        </tr>
        <tr>
            <td class="labelcenter" width="10%">Código</td>
            <td class="labelcenter" width="35%">Descrição</td>
            <td class="labelcenter" width="10%">Código</td>
            <td class="labelcenter" width="35%">Descrição</td>
        </tr>
<?php
        $cont = $paginacao->contador();

        while (!$conn->eof()) {
                $codGrupof     = trim($conn->pegaCampo("cod_grupo"));
                $nomGrupof     = trim($conn->pegaCampo("nom_grupo"));
                $codNaturezaf  = trim($conn->pegaCampo("cod_natureza"));
                $nomNaturezaf  = trim($conn->pegaCampo("nom_natureza"));
                $codPlano      = trim($conn->pegaCampo("cod_plano"));
                $chave = $codGrupof."-".$codNaturezaf."-".$codPlano;
                $conn->vaiProximo();
?>
                <tr>
                    <td class="labelcenter" width="5%"><?=$cont++;?></td>
                    <td class="show_dados_right">&nbsp;<?=$codNaturezaf;?></td>
                    <td class="show_dados">&nbsp;<?=$nomNaturezaf;?></td>
                    <td class="show_dados_right">&nbsp;<?=$codGrupof;?></td>
                    <td class="show_dados">&nbsp;<?=$nomGrupof;?></td>
                    <td class="botao" title="Excluir">
                        <a href='#' onClick="alertaQuestao('../../../../../../gestaoPatrimonial/fontes/PHP/patrimonio/patrimonio/configuracao/excluiGrupo.php?<?=Sessao::getId();?>','chave','<?=$chave;?>','Grupo: <?=$nomGrupof;?>','sn_excluir','<?=Sessao::getId();?>')">
                        <img src='../../../../../../gestaoAdministrativa/fontes/PHP/framework/temas/padrao/imagens/btnexcluir.gif'  border="0">
                        </a>
                    </td>
                </tr>
<?php
        }

?>
        </table>
<?php
        $conn->limpaSelecao();
        $conn->fechaBD();

        echo "<table width=100% align=center><tr><td align=center><font size=2>";
        $paginacao->mostraLinks();
        echo "</font></tr></td></table>";

    break;

    // executa exclusao do Grupo selecionado
    case 2:
        $variaveis = explode("-",$chave);
        $codGrupo = $variaveis[0];
        $codNatureza = $variaveis[1];
        $codPlano = $variaveis[2];

        $nomGrupo = pegaDado("nom_grupo","patrimonio.grupo","where cod_natureza = '".$codNatureza."' and cod_grupo = '".$codGrupo."'");

        include_once '../configPatrimonio.class.php';

        $patrimonio = new configPatrimonio;

        $patrimonio->setaVariaveisDeletaGrupo($codGrupo, $codNatureza, $codPlano, $anoE);

        if ($patrimonio->deleteGrupo()) {
            include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/auditoriaLegada.class.php';
            $audicao = new auditoriaLegada;
            $audicao->setaAuditoria(Sessao::read('numCgm'), $sessao->acao, $codGrupo);
            $audicao->insereAuditoria();

            $objeto = "Grupo: ".$nomGrupo;
            echo '
                <script type="text/javascript">
                    alertaAviso("'.$objeto.'","excluir","aviso","'.Sessao::getId().'");
                    mudaTelaPrincipal("excluiGrupo.php?'.Sessao::getId().'&ctrl=1&pagina='.$pagina.'");
                </script>';

        } else {

            $objeto = "Este Grupo está sendo utilizado. Grupo: ".$nomGrupo;
            echo '
                <script type="text/javascript">
                    alertaAviso("'.$objeto.'","n_excluir","erro","'.Sessao::getId().'");
                    window.location = "excluiGrupo.php?'.Sessao::getId().'";
                </script>';
        }
    break;
}

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/rodapeLegado.php';
?>

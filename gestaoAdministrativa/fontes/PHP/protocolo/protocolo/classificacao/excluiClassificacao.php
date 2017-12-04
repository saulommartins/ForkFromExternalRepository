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
* Arquivo de implementação de manutenção de classificação
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 29071 $
$Name$
$Author: rodrigosoares $
$Date: 2008-04-08 18:24:46 -0300 (Ter, 08 Abr 2008) $

Casos de uso: uc-01.06.94
*/

include '../../../framework/include/cabecalho.inc.php';
include (CAM_FRAMEWORK."legado/funcoesLegado.lib.php"    );
include (CAM_FRAMEWORK."legado/paginacaoLegada.class.php");
include (CAM_FRAMEWORK."legado/auditoriaLegada.class.php");
include '../configProtocolo.class.php';
setAjuda('uc-01.06.94');

if (!(isset($_REQUEST["ctrl"]))) {
    $ctrl = 0;
} else {
    $ctrl = $_REQUEST["ctrl"];
}

if (isset($_REQUEST["codClassificacao"])) {
    $ctrl = 1;
}
?>
 <script type="text/javascript">
 function zebra(id, classe)
 {
       var tabela = document.getElementById(id);
        var linhas = tabela.getElementsByTagName("tr");
            for (var i = 0; i < linhas.length; i++) {
            ((i%2) == 0) ? linhas[i].className = classe : void(0);
        }
    }
</script>
<?php
switch ($ctrl) {
case 0:

    if (isset($_REQUEST["acao"])) {
            $sSQLs = "SELECT cod_classificacao, nom_classificacao FROM sw_classificacao";
            Sessao::write('sSQLs',$sSQLs);
    }
        $paginacao = new paginacaoLegada;
        $paginacao->pegaDados(Sessao::read('sSQLs'),"10");
        $paginacao->pegaPagina($_REQUEST["pagina"]);
        $paginacao->geraLinks();
        $paginacao->pegaOrder("nom_classificacao","ASC");
        $sSQL = $paginacao->geraSQL();
        //print $sSQL;
        $dbEmp = new dataBaseLegado;
        $dbEmp->abreBD();
        $dbEmp->abreSelecao($sSQL);
        $dbEmp->vaiPrimeiro();
        $exec = "";
        $exec = "";
        $exec .= "
        <table width=100% id='processos'>
            <tr>
                <td class=alt_dados colspan=4>Registros de classificação</td>
            </tr>
            <tr>
                <td class=labelcenterCabecalho width=5%>&nbsp;</td>
                <td class=labelcenterCabecalho width=12%>Código</td>
                <td class=labelcenterCabecalho width=80%>Descrição</td>
                <td class=labelcenterCabecalho>&nbsp;</td>
            </tr>";
    $cont = 1;
        while (!$dbEmp->eof()) {
                $codClassificacaof  = trim($dbEmp->pegaCampo("cod_classificacao"));
                $nomClassificacaof  = trim($dbEmp->pegaCampo("nom_classificacao"));
                $dbEmp->vaiProximo();
                $nomClassificacaof = AddSlashes($nomClassificacaof);
                $exec .= "
                    <tr>
            <td class=show_dados_center_bold>".$cont++."</td>
            <td class=show_dados>".$codClassificacaof."</td>
            <td class=show_dados>".$nomClassificacaof."</td>
                    <td class=botao width=20>
                    <a href='' onClick=\"alertaQuestao('".CAM_PROTOCOLO."protocolo/classificacao/excluiClassificacao.php?".str_replace("&","*_*",Sessao::getId())."*_*codClassificacao=".$codClassificacaof."*_*stDescQuestao=".addslashes(urlencode($nomClassificacaof))."','sn_excluir','".Sessao::getId()."');\">




                    <img src='".CAM_FW_IMAGENS."btnexcluir.gif' border=0></a>
                    </td>
                    </tr>\n";
        }
        $exec .= "</table>";
        $dbEmp->limpaSelecao();
        $dbEmp->fechaBD();
        echo "$exec";
        echo "<table id= 'paginacao' width=695 align=center><tr><td align=center><font size=2>";
        $paginacao->mostraLinks();
        echo "</font></tr></td></table>";
?>
        <script>zebra('processos','zb');</script>
<?php
break;
case 1:
    $protocolo = new configProtocolo;
    $protocolo->setaVariaveisClassificacao($_REQUEST["codClassificacao"]);
    if ($protocolo->excluiClassificacao()) {
                    $audicao = new auditoriaLegada;
                    $audicao->setaAuditoria(Sessao::read('numCgm'), Sessao::read('acao'), $_REQUEST["codClassificacao"]);
                    $audicao->insereAuditoria();
                    echo '<script type="text/javascript">
                    alertaAviso("'.urlencode($_REQUEST["stDescQuestao"]).'","excluir","aviso", "'.Sessao::getId().'");
                    window.location = "excluiClassificacao.php?'.Sessao::getId().'";
                    </script>';
                    } else {
                    echo '<script type="text/javascript">
                    alertaAviso("A Classificação '.urlencode($_REQUEST["stDescQuestao"]).' não pode ser excluída porque está sendo utilizada!","n_excluir","erro", "'.Sessao::getId().'");
                    window.location = "excluiClassificacao.php?'.Sessao::getId().'";
                    </script>';
                    }
break;
}
?>

<?php
include '../../../framework/include/rodape.inc.php';
?>
